<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Item;
use App\Models\Stone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workOrders = WorkOrder::with('items')->latest()->paginate(20);
        return view('work_orders.index', compact('workOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // If source_item_id is passed, pre-select it
        $sourceItem = null;
        if ($request->has('source_item_id')) {
            $sourceItem = Item::with('stone', 'batch')->find($request->source_item_id);
        }

        // Get materials that have available items, structured hierarchically
        $materialsWithStock = Stone::whereHas('batches.items', function ($query) {
            $query->where('status', 'available');
        })->with([
                    'batches' => function ($query) {
                        $query->whereHas('items', function ($q) {
                            $q->where('status', 'available');
                        })->with([
                                    'items' => function ($q) {
                                        $q->where('status', 'available');
                                    }
                                ]);
                    }
                ])->get();

        // Get all materials for output selection
        $materials = Stone::all();

        return view('work_orders.create', compact('sourceItem', 'materialsWithStock', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_item_id' => 'required|exists:items,id',
            'description' => 'nullable|string',
            'outputs' => 'required|array|min:1',
            'outputs.*.material_id' => 'required|exists:stones,id',
            'outputs.*.width_in' => 'nullable|numeric|min:0.1',
            'outputs.*.length_in' => 'nullable|numeric|min:0.1',
            'outputs.*.thickness_mm' => 'nullable|numeric|min:0.1',
            'outputs.*.price_per_sqft' => 'nullable|numeric|min:0',
            'outputs.*.location' => 'nullable|string|max:255',
            'outputs.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create Work Order
            $workOrder = WorkOrder::create([
                'status' => 'completed', // Immediate completion for this workflow
                'description' => $validated['description'] ?? 'Fabrication',
                'source_item_id' => $validated['source_item_id'],
            ]);

            // 2. Process Source Item
            $sourceItem = Item::findOrFail($validated['source_item_id']);
            $sourceItem->status = 'consumed'; // Or 'cut'
            // Do NOT link source to this WO via work_order_id, as that field tracks CREATION.
            // The link is established via the children's parent_item_id.
            $sourceItem->save();

            // 3. Create Output Items
            foreach ($validated['outputs'] as $outputData) {
                for ($i = 0; $i < $outputData['quantity']; $i++) {
                    Item::create([
                        'stone_id' => $outputData['material_id'] ?? $sourceItem->stone_id,
                        'batch_id' => $sourceItem->batch_id,
                        'parent_item_id' => $sourceItem->id,
                        'work_order_id' => $workOrder->id,
                        'width_in' => $outputData['width_in'] ?? $sourceItem->width_in,
                        'length_in' => $outputData['length_in'] ?? $sourceItem->length_in,
                        'thickness_mm' => $outputData['thickness_mm'] ?? $sourceItem->thickness_mm,
                        'quantity_pieces' => 1, // Individual tracking
                        'price_per_sqft' => $outputData['price_per_sqft'] ?? $sourceItem->price_per_sqft,
                        'location' => $outputData['location'] ?? $sourceItem->location,
                        'status' => 'available',
                        // Barcode/QR generated automatically by model boot
                    ]);
                }
            }
        });

        return redirect()->route('work_orders.index')->with('success', 'Fabrication completed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load('items.parent.stone'); // Load outputs and their parents
        return view('work_orders.show', compact('workOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrder $workOrder)
    {
        // 1. Find the source item
        $sourceItem = $workOrder->sourceItem;
        if (!$sourceItem) {
            // Fallback for old records
            $firstChild = Item::where('work_order_id', $workOrder->id)->first();
            if ($firstChild && $firstChild->parent_item_id) {
                $sourceItem = Item::with('stone', 'batch')->find($firstChild->parent_item_id);
            }
        }

        // 2. Get materials that have available items OR the current source item
        $sourceItemId = $sourceItem ? $sourceItem->id : 0;

        $materialsWithStock = Stone::whereHas('batches.items', function ($query) use ($sourceItemId) {
            $query->where('status', 'available')->orWhere('id', $sourceItemId);
        })->with([
                    'batches' => function ($query) use ($sourceItemId) {
                        $query->whereHas('items', function ($q) use ($sourceItemId) {
                            $q->where('status', 'available')->orWhere('id', $sourceItemId);
                        })->with([
                                    'items' => function ($q) use ($sourceItemId) {
                                        $q->where('status', 'available')->orWhere('id', $sourceItemId);
                                    }
                                ]);
                    }
                ])->get();

        // 3. Get all materials for output selection
        $materials = Stone::all();

        // 4. Get existing outputs
        $outputs = Item::where('work_order_id', $workOrder->id)->get();

        return view('work_orders.edit', compact('workOrder', 'sourceItem', 'materialsWithStock', 'materials', 'outputs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'source_item_id' => 'required|exists:items,id',
            'description' => 'nullable|string',
            'outputs' => 'required|array|min:1',
            'outputs.*.material_id' => 'required|exists:stones,id',
            'outputs.*.width_in' => 'required|numeric|min:0.1',
            'outputs.*.length_in' => 'required|numeric|min:0.1',
            'outputs.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $workOrder) {
            // 1. Update Work Order details
            $workOrder->update([
                'description' => $validated['description'] ?? $workOrder->description,
            ]);

            // 2. Handle Source Item Change (if any)
            // Find current source item
            $currentSourceId = $workOrder->source_item_id;

            // Fallback if not set
            if (!$currentSourceId) {
                $currentFirstChild = Item::where('work_order_id', $workOrder->id)->first();
                $currentSourceId = $currentFirstChild ? $currentFirstChild->parent_item_id : null;
            }

            if ($currentSourceId != $validated['source_item_id']) {
                // Restore old source item
                if ($currentSourceId) {
                    $oldSource = Item::find($currentSourceId);
                    if ($oldSource) {
                        $oldSource->status = 'available';
                        $oldSource->save();
                    }
                }
                // Consume new source item
                $newSource = Item::findOrFail($validated['source_item_id']);
                $newSource->status = 'consumed';
                $newSource->save();

                // Update work order source
                $workOrder->update(['source_item_id' => $validated['source_item_id']]);
            }

            $sourceItem = Item::findOrFail($validated['source_item_id']);

            // 3. Delete old outputs
            Item::where('work_order_id', $workOrder->id)->delete();

            // 4. Create new outputs
            foreach ($validated['outputs'] as $outputData) {
                for ($i = 0; $i < $outputData['quantity']; $i++) {
                    Item::create([
                        'stone_id' => $outputData['material_id'], // Use selected material
                        'batch_id' => $sourceItem->batch_id,
                        'parent_item_id' => $sourceItem->id,
                        'work_order_id' => $workOrder->id,
                        'width_in' => $outputData['width_in'],
                        'length_in' => $outputData['length_in'],
                        'thickness_mm' => $sourceItem->thickness_mm, // Inherit or allow override? Inherit for now.
                        'quantity_pieces' => 1,
                        'price_per_sqft' => $sourceItem->price_per_sqft, // Inherit cost
                        'location' => $sourceItem->location,
                        'status' => 'available',
                    ]);
                }
            }
        });

        return redirect()->route('work_orders.index')->with('success', 'Fabrication job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrder $workOrder)
    {
        DB::transaction(function () use ($workOrder) {
            // 1. Delete all output items created by this work order
            // We use forceDelete or delete depending on if we want soft deletes. 
            // Assuming standard delete for now.
            Item::where('work_order_id', $workOrder->id)->delete();

            // 2. Restore the source item
            $sourceItem = $workOrder->sourceItem;

            if (!$sourceItem) {
                // Fallback logic
                $firstChild = Item::where('work_order_id', $workOrder->id)->first();
                if ($firstChild && $firstChild->parent_item_id) {
                    $sourceItem = Item::find($firstChild->parent_item_id);
                }
            }

            if ($sourceItem) {
                $sourceItem->status = 'available';
                $sourceItem->save();
            }

            // 3. Delete the work order
            $workOrder->delete();
        });

        return redirect()->route('work_orders.index')->with('success', 'Fabrication job reverted successfully.');
    }
}
