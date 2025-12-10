<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Dashboard summary data
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::sum('total_amount');
        $totalInventoryValue = \App\Models\Batch::sum('cost_price'); // Approximate

        return view('reports.index', compact('totalOrders', 'totalRevenue', 'totalInventoryValue'));
    }

    public function sales(Request $request)
    {
        $query = \App\Models\Order::query()->with('customer');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $orders = $query->latest()->get();
        $totalRevenue = $orders->sum('total_amount');

        return view('reports.sales', compact('orders', 'totalRevenue'));
    }

    public function inventory()
    {
        // Inventory by Item Type (Stone)
        $stones = \App\Models\Stone::with([
            'items' => function ($q) {
                $q->where('status', 'available');
            }
        ])->get();

        $inventoryData = $stones->map(function ($stone) {
            $availableItems = $stone->items;
            $totalSqft = $availableItems->sum('total_sqft');
            // Value based on average purchase price
            $avgPrice = $stone->average_price;
            $estimatedValue = $totalSqft * $avgPrice;

            return [
                'name' => $stone->name,
                'type' => $stone->type,
                'count' => $availableItems->count(),
                'sqft' => $totalSqft,
                'avg_price' => $avgPrice,
                'value' => $estimatedValue,
            ];
        });

        $totalValue = $inventoryData->sum('value');

        return view('reports.inventory', compact('inventoryData', 'totalValue'));
    }
    public function qrCodes(Request $request)
    {
        $query = \App\Models\Item::query()->with(['stone', 'batch']);

        // Filter by Stone (Material)
        if ($request->filled('stone_id')) {
            $query->where('stone_id', $request->stone_id);
        }

        // Filter by Batch (Lot)
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        // Filter by Status (default to available if not specified, unless 'all' is selected)
        if ($request->filled('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        } else {
            $query->where('status', 'available');
        }

        $items = $query->orderBy('stone_id')
            ->orderBy('batch_id')
            ->orderBy('slab_number')
            ->paginate(50);

        // Data for filters
        $stones = \App\Models\Stone::orderBy('name')->get();
        $batches = \App\Models\Batch::with('stone')->orderBy('created_at', 'desc')->get();

        return view('reports.qrcodes', compact('items', 'stones', 'batches'));
    }
    public function qrCodesStones()
    {
        // Fetch all stones (materials)
        $stones = \App\Models\Stone::orderBy('name')->get();

        return view('reports.qrcodes_stones', compact('stones'));
    }
}
