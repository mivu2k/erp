<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stone;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['stone', 'batch'])
            ->selectRaw('min(id) as id, stone_id, batch_id, width_in, length_in, thickness_mm, status, location, count(*) as quantity')
            ->groupBy('stone_id', 'batch_id', 'width_in', 'length_in', 'thickness_mm', 'status', 'location')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        $stones = Stone::all();
        return view('items.create', compact('stones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stone_id' => 'required|exists:stones,id',
            'width_in' => 'required|numeric|min:1',
            'length_in' => 'required|numeric|min:1',
            'thickness_mm' => 'nullable|numeric',
            'quantity_pieces' => 'required|integer|min:1',
            'price_per_sqft' => 'required|numeric|min:0',
            'location' => 'nullable|string',
            'bulk_mode' => 'nullable|in:on,true,1',
            'bulk_count' => 'nullable|integer|min:2',
        ]);

        $isBulk = $request->has('bulk_mode') && $request->bulk_mode;
        $count = $isBulk ? (int) $request->bulk_count : 1;

        // If bulk mode, force quantity_pieces to 1 for each item (since we are creating N items)
        if ($isBulk) {
            $validated['quantity_pieces'] = 1;
        }

        DB::transaction(function () use ($validated, $count) {
            for ($i = 0; $i < $count; $i++) {
                $item = Item::create($validated);

                InventoryMovement::create([
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'add',
                    'quantity_change' => $item->quantity_pieces,
                    'sqft_change' => $item->total_sqft,
                    'description' => 'Initial stock (Web)' . ($count > 1 ? " - Bulk $i" : ''),
                ]);
            }
        });

        return redirect()->route('items.index')->with('success', "$count Item(s) created successfully");
    }

    public function show(Item $item)
    {
        $item->load(['stone', 'movements.user']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $stones = Stone::all();
        return view('items.edit', compact('item', 'stones'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'stone_id' => 'required|exists:stones,id',
            'width_in' => 'required|numeric|min:1',
            'length_in' => 'required|numeric|min:1',
            'thickness_mm' => 'nullable|numeric',
            'quantity_pieces' => 'required|integer|min:1',
            'price_per_sqft' => 'required|numeric|min:0',
            'location' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $item->update($validated);

        return redirect()->route('materials.show', $item->stone_id)->with('success', 'Item reserved successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->status !== 'available') {
            return back()->withErrors(['error' => 'Cannot delete item that is not available (sold or reserved).']);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id',
        ]);

        $count = 0;
        foreach ($validated['ids'] as $id) {
            $item = Item::find($id);

            // If item not found (already deleted), skip
            if (!$item)
                continue;

            // Define the group criteria based on the index page grouping
            // Group By: stone_id, batch_id, width_in, length_in, thickness_mm, status, location
            $query = Item::where('stone_id', $item->stone_id)
                ->where('width_in', $item->width_in)
                ->where('length_in', $item->length_in)
                ->where('status', 'available'); // Only delete available items

            // Handle nullable fields
            if ($item->batch_id) {
                $query->where('batch_id', $item->batch_id);
            } else {
                $query->whereNull('batch_id');
            }

            if ($item->thickness_mm) {
                $query->where('thickness_mm', $item->thickness_mm);
            } else {
                $query->whereNull('thickness_mm');
            }

            if ($item->location) {
                $query->where('location', $item->location);
            } else {
                $query->whereNull('location');
            }

            // Delete all matching items in the group
            $deleted = $query->delete();
            $count += $deleted;
        }

        if ($count === 0) {
            return back()->withErrors(['error' => 'No eligible items were deleted. Only available items can be deleted.']);
        }

        return redirect()->route('items.index')->with('success', "$count items deleted successfully.");
    }

    public function showGroup(Item $item)
    {
        // Define the group criteria based on the index page grouping
        $query = Item::where('stone_id', $item->stone_id)
            ->where('width_in', $item->width_in)
            ->where('length_in', $item->length_in)
            ->where('status', $item->status);

        // Handle nullable fields
        if ($item->batch_id) {
            $query->where('batch_id', $item->batch_id);
        } else {
            $query->whereNull('batch_id');
        }

        if ($item->thickness_mm) {
            $query->where('thickness_mm', $item->thickness_mm);
        } else {
            $query->whereNull('thickness_mm');
        }

        if ($item->location) {
            $query->where('location', $item->location);
        } else {
            $query->whereNull('location');
        }

        $items = $query->get();

        return view('items.group', compact('items', 'item'));
    }

    // Custom Actions

    public function scan()
    {
        return view('scan');
    }

    public function lookup($barcode)
    {
        $barcode = trim($barcode);
        // 1. Try to decode as Base64 (QR Payload)
        $decoded = base64_decode($barcode, true);

        if ($decoded && str_contains($decoded, '|')) {
            // Format: Barcode|Timestamp|Signature
            $parts = explode('|', $decoded);

            if (count($parts) === 3) {
                $itemBarcode = $parts[0];
                $timestamp = $parts[1];
                $providedSignature = $parts[2];

                // Reconstruct data to sign
                $data = $itemBarcode . '|' . $timestamp;
                $expectedSignature = hash_hmac('sha256', $data, config('app.key'));

                if (!hash_equals($expectedSignature, $providedSignature)) {
                    return redirect('/scan')->withErrors(['error' => 'Invalid QR Signature! Potential forgery detected.']);
                }

                $barcode = $itemBarcode;
            }
        }

        // Check for Stone (Item Type) Barcode
        if (str_starts_with($barcode, 'STN-')) {
            $stone = \App\Models\Stone::where('barcode', $barcode)->first();
            if ($stone) {
                return redirect()->route('materials.show', $stone);
            }
        }

        $item = Item::where('barcode', $barcode)->first();

        if ($item) {
            return redirect()->route('items.show', $item);
        }

        return redirect('/scan')->withErrors(['error' => 'Item not found with barcode: ' . $barcode]);
    }

    public function reserve(Request $request, Item $item)
    {
        if ($item->status !== 'available') {
            return back()->withErrors(['error' => 'Item is not available for reservation.']);
        }

        $item->update(['status' => 'reserved']);

        InventoryMovement::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'reserve',
            'quantity_change' => 0,
            'sqft_change' => 0,
            'description' => 'Reserved for customer (Web)',
        ]);

        return back()->with('success', 'Item reserved successfully.');
    }

    public function sell(Request $request, Item $item)
    {
        if ($item->status === 'sold') {
            return back()->withErrors(['error' => 'Item is already sold.']);
        }

        $item->update(['status' => 'sold']);

        InventoryMovement::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'sell',
            'quantity_change' => -($item->quantity_pieces),
            'sqft_change' => -($item->total_sqft),
            'description' => 'Sold (Web)',
        ]);

        return back()->with('success', 'Item sold successfully.');
    }
}
