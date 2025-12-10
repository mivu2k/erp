@extends('layouts.app')

@section('content')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Fabrication Job #{{ $workOrder->id }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Created on {{ $workOrder->created_at->format('M d, Y H:i') }}
                <span class="mx-2">&bull;</span>
                <span
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                    {{ ucfirst($workOrder->status) }}
                </span>
            </p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('work_orders.index') }}"
                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back
                to List</a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Source Items (Inputs) -->
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Source Material (Consumed)</h3>
            </div>
            <div class="border-t border-gray-200">
                <ul role="list" class="divide-y divide-gray-200">
                    @php
                        // Get unique parent items from the output items
                        $sourceItems = $workOrder->items->map->parent->unique('id')->filter();
                        $totalInputSqft = $sourceItems->sum('sqft');
                    @endphp
                    @forelse ($sourceItems as $item)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="truncate text-sm font-medium text-indigo-600">{{ $item->stone->name }}</p>
                                <div class="ml-2 flex flex-shrink-0">
                                    <p
                                        class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                        Consumed
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-barcode mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                                        {{ $item->barcode }}
                                    </p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                        <i class="fas fa-ruler-combined mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                                        {{ $item->width_in }}" x {{ $item->length_in }}"
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>
                                        {{ number_format($item->sqft, 2) }} sqft
                                    </p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-sm text-gray-500">No source item recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Output Items (Created) -->
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Output Items (Created)</h3>
            </div>
            <div class="border-t border-gray-200">
                <ul role="list" class="divide-y divide-gray-200">
                    @php
                        $totalOutputSqft = $workOrder->items->sum('total_sqft');
                    @endphp
                    @foreach ($workOrder->items as $item)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="truncate text-sm font-medium text-indigo-600">{{ $item->stone->name }}</p>
                                <div class="ml-2 flex flex-shrink-0">
                                    <p
                                        class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                        Created
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-barcode mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                                        {{ $item->barcode }}
                                    </p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                        <i class="fas fa-ruler-combined mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                                        {{ $item->width_in }}" x {{ $item->length_in }}"
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>
                                        {{ number_format($item->sqft, 2) }} sqft
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Yield Summary -->
    <div class="mt-6 overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Yield Summary</h3>
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Total Input Area</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        {{ number_format($totalInputSqft ?? 0, 2) }} sqft</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Total Output Area</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        {{ number_format($totalOutputSqft ?? 0, 2) }} sqft</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Waste / Loss</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        @php
                            $waste = ($totalInputSqft ?? 0) - ($totalOutputSqft ?? 0);
                            $wastePct = ($totalInputSqft > 0) ? ($waste / $totalInputSqft) * 100 : 0;
                        @endphp
                        {{ number_format($waste, 2) }} sqft ({{ number_format($wastePct, 1) }}%)
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@endsection