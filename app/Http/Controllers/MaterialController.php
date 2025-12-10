<?php

namespace App\Http\Controllers;

use App\Models\Stone;
use Illuminate\Http\Request;
use App\Models\Item; // Added for the new show method
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Added for the new show method

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stones = Stone::withCount(['batches', 'items'])->paginate(20);
        return view('materials.index', compact('stones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Stone::create($validated);

        return redirect()->route('materials.index')->with('success', 'Material created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stone $material, Request $request)
    {
        // Load batches that have available items, and load those items
        $batches = $material->batches()
            ->whereHas('items', function ($query) {
                $query->where('status', 'available');
            })
            ->with([
                'items' => function ($query) {
                    $query->where('status', 'available')->orderBy('slab_number');
                },
                'supplier'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Handle Stacked View Grouping
        if ($request->get('view') === 'stacked') {
            foreach ($batches as $batch) {
                $batch->groupedItems = $batch->items->groupBy(function ($item) {
                    return $item->width_in . 'x' . $item->length_in;
                })->map(function ($items, $dimensions) {
                    return [
                        'dimensions' => $dimensions,
                        'width' => $items->first()->width_in,
                        'length' => $items->first()->length_in,
                        'count' => $items->count(),
                        'total_sqft' => $items->sum('total_sqft'),
                        'barcodes' => $items->pluck('barcode')->toArray(),
                        'items' => $items // Keep original items if needed
                    ];
                });
            }
        }

        // Calculate Average Price: Total Cost of Batches / Total SqFt of Items in those Batches
        // Note: This assumes 'cost_price' on Batch is the total cost for the block.
        $totalCost = $material->batches()->sum('cost_price');
        // We need total sqft of ALL items in those batches (including consumed ones) to get the correct initial cost per sqft
        // OR if cost_price is for the whole block, we divide by the block's total sqft.
        // Assuming Batch model doesn't store total sqft, we sum the items.

        $totalSqft = Item::whereIn('batch_id', $material->batches()->pluck('id'))->sum('total_sqft');

        $averagePrice = $totalSqft > 0 ? $totalCost / $totalSqft : 0;

        // Generate QR Code for the Material Type
        $qrCode = QrCode::size(200)->generate($material->name);

        return view('materials.show', [
            'stone' => $material,
            'averagePrice' => $averagePrice,
            'qrCode' => $qrCode,
            'batches' => $batches,
            'viewMode' => $request->get('view', 'default')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stone $material)
    {
        return view('materials.edit', ['stone' => $material]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stone $material)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stone $material)
    {
        if ($material->batches()->exists()) {
            return back()->with('error', 'Cannot delete material with existing lots.');
        }

        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material deleted successfully.');
    }
}
