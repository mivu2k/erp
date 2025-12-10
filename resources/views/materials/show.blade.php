@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $stone->name }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ $stone->type }}
                    @if($stone->color) • {{ $stone->color }} @endif
                    @if($stone->size) • {{ $stone->size }} @endif
                    @if($stone->color_finish) • {{ $stone->color_finish }} @endif
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('materials.edit', $stone) }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                <button onclick="window.print()"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Print Label</button>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono">{{ $stone->barcode }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Color</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $stone->color ?? 'N/A' }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Size / Thickness</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $stone->size ?? 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $stone->description }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Average Purchase Price</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $currency_symbol }}{{ number_format($stone->average_price, 2) }}/sqft
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Total Stock</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $stone->items()->where('status', 'available')->count() }} Slabs
                        ({{ number_format($stone->items()->where('status', 'available')->sum('total_sqft'), 2) }} SqFt)
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">QR Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="visible-print">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($stone->qrcode_payload) !!}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Scan to view details</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Available Slabs List -->
    <div class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Available Slabs</h3>
            <div class="flex space-x-2">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'default']) }}"
                    class="px-3 py-1 rounded-md text-sm font-medium {{ $viewMode == 'default' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }}">
                    Default View
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'stacked']) }}"
                    class="px-3 py-1 rounded-md text-sm font-medium {{ $viewMode == 'stacked' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }}">
                    Stacked View
                </a>
            </div>
        </div>

        @forelse($batches as $batch)
            <div class="mb-8 bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
                <div class="px-4 py-4 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">
                            Lot: {{ $batch->block_number }}
                        </h4>
                        <p class="text-sm text-gray-500">
                            Received: {{ $batch->arrival_date }}
                            @if($batch->supplier) • Supplier: {{ $batch->supplier->name }} @endif
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $batch->items->count() }} Slabs Available
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            @if($viewMode == 'stacked')
                                <tr>
                                    <th scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 sm:pl-6">
                                        Quantity</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                        Dimensions</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Total
                                        Area (SqFt)</th>
                                    <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">View</span>
                                    </th>
                                </tr>
                            @else
                                <tr>
                                    <th scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 sm:pl-6">
                                        Slab #</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                        Dimensions</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Area
                                        (SqFt)</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                        Barcode</th>
                                    <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">View</span>
                                    </th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if($viewMode == 'stacked')
                                @foreach($batch->groupedItems as $group)
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $group['count'] }} Slabs
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                            {{ $group['width'] }}" x {{ $group['length'] }}"
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                            {{ number_format($group['total_sqft'], 2) }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <span class="text-xs text-gray-400 italic">Grouped View</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($batch->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            #{{ $item->slab_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                            {{ $item->width_in }}" x {{ $item->length_in }}"
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                            {{ number_format($item->total_sqft, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-sm font-mono text-gray-500">
                                            {{ $item->barcode }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <a href="{{ route('items.show', $item) }}"
                                                class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white rounded-lg shadow">
                <p class="text-gray-500">No available slabs found for this material.</p>
            </div>
        @endforelse
    </div>

    <!-- Print Label Template (Hidden by default, visible on print) -->
    <div id="print-label" class="hidden">
        <div class="flex flex-col items-center justify-center h-full text-center">
            <h1 class="text-6xl font-bold mb-8 text-black">{{ $stone->name }}</h1>
            <div class="mb-4">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(400)->generate($stone->qrcode_payload) !!}
            </div>
            <p class="text-2xl font-mono text-black">{{ $stone->barcode }}</p>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-label,
            #print-label * {
                visibility: visible;
            }

            #print-label {
                display: block !important;
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: white;
                z-index: 9999;
                padding: 2rem;
            }

            /* Hide the print button itself if it was somehow visible */
            button {
                display: none;
            }
        }
    </style>
@endsection