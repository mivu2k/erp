<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'user'])->latest()->paginate(20);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $stones = \App\Models\Stone::all();

        // 1. Get real batches with available items
        $batches = \App\Models\Batch::whereHas('items', function ($query) {
            $query->where('status', 'available');
        })->with([
                    'stone',
                    'items' => function ($query) {
                        $query->where('status', 'available');
                    }
                ])->get();

        // 2. Get loose items (no batch) that are available
        $looseItems = Item::whereNull('batch_id')
            ->where('status', 'available')
            ->with('stone')
            ->get()
            ->groupBy('stone_id');

        // 3. Create virtual batches for loose items
        foreach ($looseItems as $stoneId => $items) {
            $virtualBatch = new \stdClass();
            $virtualBatch->id = 'loose_' . $stoneId; // String ID to distinguish
            $virtualBatch->stone_id = $stoneId;
            $virtualBatch->block_number = 'No Lot / Loose';
            $virtualBatch->items = $items;

            // Add to batches collection
            $batches->push($virtualBatch);
        }

        return view('orders.create', compact('customers', 'stones', 'batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'vehicle_no' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_ids' => 'required|array|min:1', // Changed from item_id to item_ids array
            'items.*.item_ids.*' => 'exists:items,id',
            'items.*.length_in' => 'required|numeric|min:0',
            'items.*.width_in' => 'required|numeric|min:0',
            'items.*.thickness_mm' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => 'confirmed',
                'delivery_date' => $validated['date'],
                'notes' => "Vehicle: " . ($validated['vehicle_no'] ?? 'N/A'),
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            $processedItems = [];

            foreach ($validated['items'] as $groupData) {
                // Iterate through each ID in the group
                foreach ($groupData['item_ids'] as $itemId) {
                    $item = Item::lockForUpdate()->find($itemId);

                    // Check if we already processed this item in this transaction
                    if (in_array($item->id, $processedItems)) {
                        continue; // Skip duplicates
                    }

                    if ($item->status !== 'available') {
                        throw new \Exception("Item #{$item->slab_number} is no longer available.");
                    }

                    // Update Item status
                    $item->update(['status' => 'sold']);

                    // Record Movement
                    InventoryMovement::create([
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'sale',
                        'quantity_change' => -1,
                        'sqft_change' => -$item->total_sqft,
                        'description' => "Sold in Order {$order->order_number}",
                    ]);

                    $processedItems[] = $item->id;

                    // Calculate Net SqFt based on input dimensions (same for all items in group)
                    $netSqft = ($groupData['length_in'] * $groupData['width_in']) / 144;

                    // Calculate Wastage
                    $wastage = max(0, $item->total_sqft - $netSqft);

                    $unitPrice = $groupData['price'];
                    // Price based on Actual Slab (Gross) Size
                    $lineTotal = $item->total_sqft * $unitPrice;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_id' => $item->id,
                        'length_in' => $groupData['length_in'],
                        'width_in' => $groupData['width_in'],
                        'thickness_mm' => $groupData['thickness_mm'],
                        'sqft' => $netSqft,
                        'wastage' => $wastage,
                        'price' => $lineTotal,
                    ]);

                    $totalAmount += $lineTotal;
                }
            }

            $order->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.item.stone', 'items.item.batch', 'user']);
        return view('orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        return view('orders.invoice', compact('order'));
    }

    public function edit(Order $order)
    {
        $customers = Customer::all();
        $stones = \App\Models\Stone::all();

        // We'll pass all available batches with their available items count for filtering in frontend
        // Also include items currently in this order so they appear if we were to re-select (though logic handles them separately)
        $batches = \App\Models\Batch::whereHas('items', function ($query) use ($order) {
            $query->where('status', 'available')
                ->orWhereIn('id', $order->items->pluck('item_id'));
        })->with([
                    'stone',
                    'items' => function ($query) use ($order) {
                        $query->where('status', 'available')
                            ->orWhereIn('id', $order->items->pluck('item_id'));
                    }
                ])->get();

        // Get loose items (no batch) that are available OR in this order
        $looseItems = Item::whereNull('batch_id')
            ->where(function ($query) use ($order) {
                $query->where('status', 'available')
                    ->orWhereIn('id', $order->items->pluck('item_id'));
            })
            ->with('stone')
            ->get()
            ->groupBy('stone_id');

        // Create virtual batches for loose items
        foreach ($looseItems as $stoneId => $items) {
            $virtualBatch = new \stdClass();
            $virtualBatch->id = 'loose_' . $stoneId;
            $virtualBatch->stone_id = $stoneId;
            $virtualBatch->block_number = 'No Lot / Loose';
            $virtualBatch->items = $items;

            $batches->push($virtualBatch);
        }

        $order->load('items.item.stone', 'items.item.batch');

        return view('orders.edit', compact('order', 'customers', 'stones', 'batches'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:quote,confirmed,production,completed',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.length_in' => 'required|numeric|min:0',
            'items.*.width_in' => 'required|numeric|min:0',
            'items.*.thickness_mm' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $order) {
            $order->update([
                'customer_id' => $validated['customer_id'],
                'status' => $validated['status'],
                'delivery_date' => $validated['delivery_date'],
                'notes' => $validated['notes'],
            ]);

            // Release currently allocated items
            foreach ($order->items as $orderItem) {
                $orderItem->item->update(['status' => 'available']);
                // We could record a reversal movement here, but for simplicity in update, 
                // we'll just re-allocate below. 
                // Ideally, we should diff the items, but full replace is safer for V2 MVP.
                InventoryMovement::create([
                    'item_id' => $orderItem->item_id,
                    'user_id' => Auth::id(),
                    'type' => 'adjustment',
                    'quantity_change' => 1,
                    'sqft_change' => $orderItem->item->total_sqft,
                    'description' => "Order {$order->order_number} updated (released)",
                ]);
            }

            $order->items()->delete();

            $totalAmount = 0;

            $processedItems = [];

            foreach ($validated['items'] as $itemData) {
                // Skip if item_id is not set (empty row)
                if (empty($itemData['item_id']))
                    continue;

                $item = Item::lockForUpdate()->find($itemData['item_id']);

                if (in_array($item->id, $processedItems)) {
                    // Already processed in this transaction
                } else {
                    if ($item->status !== 'available') {
                        // Check if it was already in this order (we released them above, so they should be available)
                        // But if another user grabbed it in milliseconds, we have a race.
                        // Since we released them in this transaction, they are available to us.
                    }

                    $item->update(['status' => $validated['status'] === 'quote' ? 'reserved' : 'sold']);

                    InventoryMovement::create([
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'sale',
                        'quantity_change' => -1,
                        'sqft_change' => -$item->total_sqft,
                        'description' => "Allocated to Order {$order->order_number} (updated)",
                    ]);

                    $processedItems[] = $item->id;
                }

                // Calculate Net SqFt based on input dimensions
                $netSqft = ($itemData['length_in'] * $itemData['width_in']) / 144;

                // Calculate Wastage
                $wastage = max(0, $item->total_sqft - $netSqft);

                $unitPrice = $itemData['price'];
                $lineTotal = $netSqft * $unitPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'length_in' => $itemData['length_in'],
                    'width_in' => $itemData['width_in'],
                    'thickness_mm' => $itemData['thickness_mm'],
                    'sqft' => $netSqft,
                    'wastage' => $wastage,
                    'price' => $lineTotal,
                ]);

                $totalAmount += $lineTotal;
            }

            $order->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $orderItem) {
                $item = $orderItem->item;

                // Revert item status to available
                $item->update(['status' => 'available']);

                // Record inventory movement (reversal)
                InventoryMovement::create([
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'adjustment', // or 'return'
                    'quantity_change' => 1,
                    'sqft_change' => $item->total_sqft,
                    'description' => "Order {$order->order_number} deleted",
                ]);
            }

            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:quote,confirmed,production,completed',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $oldStatus = $order->status;
            $newStatus = $validated['status'];

            $order->update(['status' => $newStatus]);

            // If moving from Quote to Confirmed/Production/Completed, ensure items are marked as Sold
            if ($oldStatus === 'quote' && in_array($newStatus, ['confirmed', 'production', 'completed'])) {
                foreach ($order->items as $orderItem) {
                    $orderItem->item->update(['status' => 'sold']);
                }
            }
            // If moving back to Quote, mark items as Reserved
            elseif ($newStatus === 'quote' && $oldStatus !== 'quote') {
                foreach ($order->items as $orderItem) {
                    $orderItem->item->update(['status' => 'reserved']);
                }
            }
        });

        return back()->with('success', 'Order status updated successfully.');
    }
}
