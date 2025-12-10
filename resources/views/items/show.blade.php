@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $item->stone->name }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ $item->stone->type }}
                    @if($item->stone->color) • {{ $item->stone->color }} @endif
                    @if($item->stone->size) • {{ $item->stone->size }} @endif
                    @if($item->stone->color_finish) • {{ $item->stone->color_finish }} @endif
                </p>
            </div>
            <div class="flex space-x-2">
                @if($item->status === 'available')
                    <form action="{{ route('items.reserve', $item) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Reserve</button>
                    </form>
                    <form action="{{ route('items.sell', $item) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Sell</button>
                    </form>
                @endif
                <a href="{{ route('items.edit', $item) }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                <button onclick="window.print()"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Print Label</button>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $item->barcode }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Color</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $item->stone->color ?? 'N/A' }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Size (Category)</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $item->stone->size ?? 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $item->width_in }}" x {{ $item->length_in }}" x {{ $item->thickness_mm }}mm
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">SQFT</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ number_format($item->sqft, 2) }} sqft/piece (Total: {{ number_format($item->total_sqft, 2) }})
                    </dd>
                </div>
                @php
                    $batchTotalSqFt = $item->batch ? $item->batch->items()->sum('total_sqft') : 0;
                    $unitCost = ($batchTotalSqFt > 0 && $item->batch) ? ($item->batch->cost_price / $batchTotalSqFt) : 0;
                    $itemCost = $unitCost * $item->total_sqft;
                @endphp
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Cost Price (Est.)</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $currency_symbol }} {{ number_format($unitCost, 2) }}/sqft (Total Value:
                        {{ $currency_symbol }} {{ number_format($itemCost, 2) }})
                        <br>
                        <span class="text-xs text-gray-500">Based on Lot Cost:
                            {{ $currency_symbol }}{{ number_format($item->batch->cost_price ?? 0, 2) }} /
                            {{ number_format($batchTotalSqFt, 2) }} sqft</span>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Selling Price</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $currency_symbol }} {{ number_format($item->price_per_sqft, 2) }}/sqft (Total Value:
                        {{ $currency_symbol }} {{ number_format($item->total_price, 2) }})
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">QR Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="visible-print">
                            {!! QrCode::size(100)->generate($item->qrcode_payload) !!}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Scan to verify</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@endsection