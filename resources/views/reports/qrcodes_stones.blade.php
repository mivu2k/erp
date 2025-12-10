<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material QR Codes</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
        }

        .controls {
            margin-bottom: 20px;
            text-align: center;
        }

        .controls button {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
        }

        .controls a {
            margin-left: 10px;
            text-decoration: none;
            color: #333;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* 4 columns per row */
            gap: 10px;
            page-break-after: always;
        }

        .qr-item {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: center;
            break-inside: avoid;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 180px;
            overflow: hidden;
        }

        .qr-item h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .qr-item p {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #555;
        }

        .qr-code {
            margin: 5px 0;
        }

        @media print {
            body {
                padding: 10px;
            }

            .controls {
                display: none;
            }

            .grid-container {
                gap: 5px;
            }

            .qr-item {
                border: 1px dashed #999;
                height: 160px;
            }
        }
    </style>
</head>

<body>
    <div class="controls">
        <button onclick="window.print()">Print QR Codes</button>
        <a href="{{ route('reports.index') }}">Back to Reports</a>
    </div>

    <div class="grid-container">
        @foreach($stones as $stone)
            <div class="qr-item">
                <h3>{{ $stone->name }}</h3>
                <div class="qr-code">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($stone->qrcode_payload ?? ($stone->barcode ?? 'STN-' . $stone->id)) !!}
                </div>
                <p><strong>{{ $stone->barcode }}</strong></p>
                <p>{{ $stone->color ?? '' }} {{ $stone->size ? ' - ' . $stone->size : '' }}</p>
            </div>
        @endforeach
    </div>
</body>

</html>