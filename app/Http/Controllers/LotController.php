<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Stone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = Batch::with('stone', 'supplier')->withCount('items')->latest()->get();
        return view('lots.index', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stones = Stone::all();
        $suppliers = Supplier::all();
        return view('lots.create', compact('stones', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stone_id' => 'required|exists:stones,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'block_number' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'arrival_date' => 'required|date',
            'slabs' => 'required|array|min:1',
            'slabs.*.quantity' => 'required|integer|min:1',
            'slabs.*.width_in' => 'required|numeric|min:0.1',
            'slabs.*.length_in' => 'required|numeric|min:0.1',
            'slabs.*.thickness_mm' => 'required|numeric|min:0.1',
        ]);

        // Calculate total quantity of slabs
        $totalSlabs = 0;
        foreach ($request->slabs as $slabData) {
            $totalSlabs += $slabData['quantity'];
        }
        // $validated['quantity_slabs'] = $totalSlabs; // Column does not exist

        DB::transaction(function () use ($validated, $request) {
            $batch = Batch::create($validated);

            $slabCounter = 1;
            foreach ($request->slabs as $slabData) {
                for ($i = 0; $i < $slabData['quantity']; $i++) {
                    Item::create([
                        'stone_id' => $validated['stone_id'],
                        'batch_id' => $batch->id,
                        'slab_number' => $slabCounter++, // Assign sequential slab number
                        'width_in' => $slabData['width_in'],
                        'length_in' => $slabData['length_in'],
                        'thickness_mm' => $slabData['thickness_mm'],
                        'quantity_pieces' => 1,
                        'price_per_sqft' => 0, // Initial price, maybe calculated later or 0
                        'location' => 'Warehouse', // Default location
                        'status' => 'available',
                    ]);
                }
            }
        });

        return redirect()->route('lots.index')->with('success', 'Lot created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $lot)
    {
        $lot->load(['stone', 'supplier', 'items']);
        return view('lots.show', ['batch' => $lot]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $lot)
    {
        $stones = Stone::all();
        $suppliers = Supplier::all();
        return view('lots.edit', ['batch' => $lot, 'stones' => $stones, 'suppliers' => $suppliers]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Batch $lot)
    {
        $validated = $request->validate([
            'stone_id' => 'required|exists:stones,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'block_number' => 'required|string|max:255',
            'quantity_slabs' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'arrival_date' => 'required|date',
        ]);

        $lot->update($validated);

        return redirect()->route('lots.index')->with('success', 'Lot updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $lot)
    {
        // Cascade delete items manually if not set in DB, or just allow it.
        // Assuming we want to allow deleting the batch and all its items as per user request.

        DB::transaction(function () use ($lot) {
            $lot->items()->delete();
            $lot->delete();
        });

        return redirect()->route('lots.index')->with('success', 'Lot and all associated items deleted successfully.');
    }
}
