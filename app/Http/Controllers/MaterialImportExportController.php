<?php

namespace App\Http\Controllers;

use App\Models\Stone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class MaterialImportExportController extends Controller
{
    public function export()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=materials_export_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Type', 'Color', 'Size', 'Price Per SqFt', 'Description'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $stones = Stone::all();

            foreach ($stones as $stone) {
                fputcsv($file, [
                    $stone->id,
                    $stone->name,
                    $stone->type,
                    $stone->color,
                    $stone->size,
                    $stone->price_per_sqft,
                    $stone->description
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Skip header

        $count = 0;
        $updated = 0;

        DB::transaction(function () use ($handle, &$count, &$updated) {
            while (($row = fgetcsv($handle)) !== false) {
                // Expected columns: ID, Name, Type, Color, Size, Price Per SqFt, Description
                // If ID is present and exists, update. Else create.
                // Actually, let's rely on Name + Type as unique key if ID is empty, or just ID.

                $id = $row[0] ?? null;
                $name = $row[1] ?? null;

                if (!$name)
                    continue; // Skip empty rows

                $data = [
                    'name' => $name,
                    'type' => $row[2] ?? 'Granite',
                    'color' => $row[3] ?? null,
                    'size' => $row[4] ?? null,
                    'price_per_sqft' => is_numeric($row[5] ?? null) ? $row[5] : 0,
                    'description' => $row[6] ?? null,
                ];

                if ($id && $stone = Stone::find($id)) {
                    $stone->update($data);
                    $updated++;
                } else {
                    // Try to find by name to avoid duplicates if ID not provided
                    $stone = Stone::where('name', $name)->first();
                    if ($stone) {
                        $stone->update($data);
                        $updated++;
                    } else {
                        Stone::create($data);
                        $count++;
                    }
                }
            }
        });

        fclose($handle);

        return back()->with('success', "Import completed: $count created, $updated updated.");
    }
}
