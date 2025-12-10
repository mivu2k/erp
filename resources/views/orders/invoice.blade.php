<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background-color: #ffffff;
            margin: 0;
            padding: 40px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
        }

        .company-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #111827;
        }

        .company-info p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #111827;
            letter-spacing: -0.02em;
        }

        .invoice-details p {
            margin: 0;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.6;
        }

        /* Bill To / Ship To */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .info-box h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .info-box p {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            color: #1f2937;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            background-color: #f9fafb;
            color: #4b5563;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .item-name {
            font-weight: 600;
            color: #111827;
            display: block;
            margin-bottom: 4px;
        }

        .item-meta {
            font-size: 12px;
            color: #6b7280;
            display: block;
            line-height: 1.4;
        }

        /* Totals */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
            padding-top: 16px;
        }

        .total-label {
            font-size: 14px;
            color: #6b7280;
        }

        .total-value {
            font-size: 14px;
            font-weight: 600;
            text-align: right;
            color: #1f2937;
        }

        .grand-total .total-label {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .grand-total .total-value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        /* Notes & Footer */
        .notes {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .notes h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .notes p {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 60px;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
        }

        /* Print Controls */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #111827;
            color: white;
            border: none;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .container {
                max-width: 100%;
            }

            .no-print {
                display: none;
            }

            .notes {
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact;
            }

            th {
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Back to Order</a>
        <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ \App\Models\Setting::getValue('company_name', 'Stone Inventory Co.') }}</h1>
                <p>
                    {!! nl2br(e(\App\Models\Setting::getValue('company_address', '123 Stone Lane' . PHP_EOL . 'Marble City, ST 12345'))) !!}<br>
                    {{ \App\Models\Setting::getValue('company_email', '') }}<br>
                    {{ \App\Models\Setting::getValue('company_phone', '') }}
                </p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p>
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>Due Date:</strong>
                    {{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'On Receipt' }}
                </p>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-box">
                <h3>Bill To</h3>
                <p>
                    <strong>{{ $order->customer->name }}</strong><br>
                    {{ $order->customer->email }}<br>
                    {{ $order->customer->phone ?? '' }}<br>
                    {{ $order->customer->address ?? '' }}
                </p>
            </div>
            <!-- Optional: Ship To Section if available -->
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Item Description</th>
                    <th class="text-center">Dimensions</th>
                    <th class="text-center">SqFt</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedItems = $order->items->groupBy(function ($item) {
                        return $item->item->stone_id . '|' . number_format($item->width_in, 2) . '|' . number_format($item->length_in, 2);
                    });
                @endphp

                @foreach($groupedItems as $group)
                    @php
                        $first = $group->first();
                        $count = $group->count();
                        $totalSqft = $group->sum('sqft');
                        $totalPrice = $group->sum('price');

                        $validSlabNumbers = $group->map(fn($i) => $i->item->slab_number)
                            ->filter(fn($n) => !is_null($n) && trim($n) !== '')
                            ->map(fn($n) => '#' . $n);

                        $slabDisplay = '';
                        if ($validSlabNumbers->isEmpty()) {
                            $slabDisplay = 'Unnumbered';
                        } elseif ($validSlabNumbers->count() > 5) {
                            $slabDisplay = $validSlabNumbers->take(5)->implode(', ') . '...';
                        } else {
                            $slabDisplay = $validSlabNumbers->implode(', ');
                        }
                    @endphp
                    <tr>
                        <td>
                            <span class="item-name">{{ $first->item->stone->name }}</span>
                            <span class="item-meta">
                                @if($count > 1)
                                    Qty: {{ $count }} &bull; Slabs: {{ $slabDisplay }}
                                @else
                                    Slab: {{ $first->item->slab_number ? '#' . $first->item->slab_number : 'Unnumbered' }}
                                @endif
                                <br>
                                Lot: {{ $first->item->batch->block_number ?? 'No Lot' }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{ number_format($first->width_in, 2) }}" x {{ number_format($first->length_in, 2) }}"
                            <br>
                            <span class="item-meta">Original: {{ number_format($first->item->width_in, 2) }}" x
                                {{ number_format($first->item->length_in, 2) }}"</span>
                        </td>
                        <td class="text-center">
                            {{ number_format($totalSqft, 2) }}
                        </td>
                        <td class="text-right">
                            {{ $currency_symbol }}{{ number_format($totalPrice, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Subtotal</td>
                    <td class="total-value">{{ $currency_symbol }}{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                <!-- Tax and other adjustments could go here -->
                <tr class="grand-total">
                    <td class="total-label">Total</td>
                    <td class="total-value">{{ $currency_symbol }}{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($order->notes)
            <div class="notes">
                <h3>Notes</h3>
                <p>{{ $order->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>

</html>