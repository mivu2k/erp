<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_items' => Item::sum('quantity_pieces'),
            'total_sqft' => Item::sum('total_sqft'),
            'total_value' => Item::sum('total_value'),
        ];

        $recent_movements = InventoryMovement::with(['item', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $recent_orders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_movements', 'recent_orders'));
    }

    public function items(Request $request)
    {
        $items = Item::with('stone')->latest()->paginate(20);
        return view('items.index', compact('items'));
    }

    public function createItem()
    {
        return view('items.create');
    }

    public function storeItem(Request $request)
    {
        // Reuse logic from InventoryController or call it directly?
        // For simplicity, we'll duplicate or refactor. Let's duplicate for now to keep it simple in WebController
        // Ideally, we should have a Service class.

        $validated = $request->validate([
            'stone_id' => 'required|exists:stones,id',
            'width_in' => 'required|numeric|min:1',
            'length_in' => 'required|numeric|min:1',
            'thickness_mm' => 'nullable|numeric',
            'quantity_pieces' => 'required|integer|min:1',
            'price_per_sqft' => 'required|numeric|min:0',
            'location' => 'nullable|string',
        ]);

        $item = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $item = Item::create($validated);

            InventoryMovement::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'add',
                'quantity_change' => $item->quantity_pieces,
                'sqft_change' => $item->total_sqft,
                'description' => 'Initial stock (Web)',
            ]);

            return $item;
        });

        return redirect('/items/' . $item->id)->with('success', 'Item created successfully');
    }

    public function showItem($id)
    {
        $item = Item::with(['stone', 'movements.user'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function scan()
    {
        return view('scan');
    }

    public function lookup($barcode)
    {
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

                // Optional: Check timestamp for expiration? (e.g. valid for 24h)
                // if (time() - $timestamp > 86400) { ... }

                // Signature valid, use the barcode
                $barcode = $itemBarcode;
            }
        }
        // Else: assume it's a plain barcode (manual entry or Code128)

        $item = Item::where('barcode', $barcode)->first();

        if ($item) {
            return redirect('/items/' . $item->id);
        }

        return redirect('/scan')->withErrors(['error' => 'Item not found with barcode: ' . $barcode]);
    }

    public function reserveItem(Request $request, Item $item)
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

    public function sellItem(Request $request, Item $item)
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
