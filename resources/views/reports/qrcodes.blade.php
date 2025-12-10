<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory QR Codes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9fafb;
        }

        /* Screen-only styles for the Filter Bar */
        .filter-bar {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            border: 1px solid #e5e7eb;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .form-control {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            min-width: 150px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #374151;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background-color: #1f2937;
        }

        .btn-print {
            background-color: #059669;
            color: white;
            margin-left: auto;
        }

        .btn-print:hover {
            background-color: #047857;
        }

        /* Grid Layout for QR Codes */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .qr-item {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 220px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .qr-item h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: bold;
            color: #111;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .qr-item .meta {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .qr-code {
            margin: 5px 0;
        }

        .qr-item .barcode {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 12px;
            margin: 5px 0 2px 0;
        }

        .qr-item .details {
            font-size: 11px;
            color: #374151;
            margin: 0;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .filter-bar {
                display: none;
            }

            .grid-container {
                display: grid;
                /* 3 columns fits well on A4 portrait */
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .qr-item {
                border: 1px dashed #ccc;
                box-shadow: none;
                break-inside: avoid;
                page-break-inside: avoid;
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="filter-bar">
        <form method="GET" action="{{ route('reports.qrcodes') }}"
            style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; flex: 1;">
            <div class="form-group">
                <label for="stone_id">Material</label>
                <select name="stone_id" id="stone_id" class="form-control">
                    <option value="">All Materials</option>
                    @foreach($stones as $stone)
                        <option value="{{ $stone->id }}" {{ request('stone_id') == $stone->id ? 'selected' : '' }}>
                            {{ $stone->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="batch_id">Lot / Batch</label>
                <select name="batch_id" id="batch_id" class="form-control">
                    <option value="">All Lots</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->block_number }} ({{ $batch->stone->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="available" {{ request('status', 'available') == 'available' ? 'selected' : '' }}>
                        Available Only</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Items</option>
                    <option value="allocated" {{ request('status') == 'allocated' ? 'selected' : '' }}>Allocated</option>
                    <option value="consumed" {{ request('status') == 'consumed' ? 'selected' : '' }}>Consumed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('reports.qrcodes') }}" class="btn"
                style="color: #6b7280; text-decoration: underline;">Reset</a>
        </form>

        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-print">🖨️ Print QR Codes</button>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="grid-container">
        @forelse($items as $item)
            <div class="qr-item">
                <h3>{{ $item->stone->name }}</h3>
                <p class="meta">
                    {{ $item->stone->color ?? '' }}
                    {{ $item->stone->size ? '- ' . $item->stone->size : '' }}
                </p>

                <div class="qr-code">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate($item->qrcode_payload ?? $item->barcode) !!}
                </div>

                <p class="barcode">{{ $item->barcode }}</p>

                <div class="details">
                    <p style="margin: 0;">Lot: {{ $item->batch->block_number ?? 'N/A' }} / #{{ $item->slab_number }}</p>
                    <p style="margin: 2px 0 0 0;">{{ $item->width_in }}" x {{ $item->length_in }}"
                        ({{ number_format($item->total_sqft, 2) }} sqft)</p>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280;">
                <p>No items found matching your filters.</p>
            </div>
        @endforelse
    </div>
    <div class="no-print" style="margin-top: 20px; padding: 0 20px;">
        {{ $items->appends(request()->query())->links() }}
    </div>
</body>

</html>