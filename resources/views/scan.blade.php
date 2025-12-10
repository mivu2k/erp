@extends('layouts.app')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-900">Scan QR/Barcode</h1>

        @if($errors->any())
            <div class="rounded-md bg-red-50 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul role="list" class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div id="reader" width="100%"></div>
                <p class="text-center text-sm text-gray-500 mt-4">Point camera at a barcode or QR code</p>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Manual Entry</h3>
                <form id="manual-form" onsubmit="handleManualSubmit(event)">
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <div class="relative flex-grow focus-within:z-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </div>
                            <input type="text" id="manual-code"
                                class="focus:ring-primary-500 focus:border-primary-500 block w-full rounded-none rounded-l-md pl-10 py-2 sm:text-sm border border-gray-300 text-gray-900"
                                placeholder="Enter Barcode / QR Data">
                        </div>
                        <button type="submit"
                            class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                            <i class="fas fa-search text-gray-400"></i>
                            <span>Go</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            let isScanning = true;

            function onScanSuccess(decodedText, decodedResult) {
                if (!isScanning) return;

                // Stop scanning to prevent multiple redirects
                isScanning = false;
                html5QrcodeScanner.clear();

                // Redirect to lookup route
                // Encode the barcode to handle special characters if any
                // Double encoding might be safer for slashes in base64, but let's try single first.
                // Laravel's routing usually handles %2F if configured, but often it's problematic.
                // A safer way for Base64 in URLs is to use query parameters or URL-safe Base64.
                // For now, we'll stick to the existing route structure but be careful.

                window.location.href = `/items/lookup/${encodeURIComponent(decodedText)}`;
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: { width: 250, height: 250 } },
                                /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);

            function handleManualSubmit(e) {
                e.preventDefault();
                const code = document.getElementById('manual-code').value.trim();
                if (code) {
                    window.location.href = `/items/lookup/${encodeURIComponent(code)}`;
                }
            }
        </script>
    @endpush
@endsection