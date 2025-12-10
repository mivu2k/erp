<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Stone;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemImportExportController extends Controller
{
    public function export()
    {
        $fileName = 'inventory_items_' . date('Y-m-d_H-i-s') . '.csv';
        $items = Item::with(['stone', 'batch'])->where('status', 'available')->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Material', 'Color', 'Lot/Block', 'Slab Number', 'Width (in)', 'Length (in)', 'Barcode', 'Status');

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                $row['Material'] = $item->stone->name;
                $row['Color'] = $item->stone->color;
                $row['Lot'] = $item->batch->block_number ?? '';
                $row['Slab #'] = $item->slab_number;
                $row['Width'] = $item->width_in;
                $row['Length'] = $item->length_in;
                $row['Barcode'] = $item->barcode;
                $row['Status'] = $item->status;

                fputcsv($file, array($row['Material'], $row['Color'], $row['Lot'], $row['Slab #'], $row['Width'], $row['Length'], $row['Barcode'], $row['Status']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $fileHandle = fopen($file->getPathname(), 'r');

        // Skip header row
        fgetcsv($fileHandle);

        DB::beginTransaction();
        try {
            $count = 0;
            while (($row = fgetcsv($fileHandle)) !== false) {
                // Expected columns: Material, Color, Lot, Slab #, Width, Length
                // Index: 0=Material, 1=Color, 2=Lot, 3=Slab, 4=Width, 5=Length

                if (count($row) < 6)
                    continue;

                $materialName = trim($row[0]);
                $color = trim($row[1]);
                $lotNumber = trim($row[2]);
                $slabNumber = trim($row[3]);
                $width = floatval($row[4]);
                $length = floatval($row[5]);

                if (empty($materialName) || empty($lotNumber))
                    continue;

                // 1. Find or Create Material (Stone)
                $stone = Stone::firstOrCreate(
                    ['name' => $materialName],
                    ['color' => $color, 'type' => 'Granite'] // Default type if new
                );

                // 2. Find or Create Batch
                $batch = Batch::firstOrCreate(
                    ['stone_id' => $stone->id, 'block_number' => $lotNumber],
                    ['cost_price' => 0, 'date_received' => now()]
                );

                // 3. Create Item
                // Check if exists to avoid duplicates
                $exists = Item::where('batch_id', $batch->id)
                    ->where('slab_number', $slabNumber)
                    ->exists();

                if (!$exists) {
                    Item::create([
                        'stone_id' => $stone->id,
                        'batch_id' => $batch->id,
                        'slab_number' => $slabNumber,
                        'width_in' => $width,
                        'length_in' => $length,
                        'barcode' => 'ITM-' . strtoupper(uniqid()), // Temp barcode
                        'status' => 'available'
                    ]);
                    $count++;
                }
            }

            DB::commit();
            fclose($fileHandle);

            return back()->with('success', "Successfully imported $count items.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Error: ' . $e->getMessage());
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
