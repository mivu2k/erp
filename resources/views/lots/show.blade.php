@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Batch Details</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Block #{{ $batch->block_number }}</p>
            </div>
            <form action="{{ route('lots.destroy', $batch) }}" method="POST"
                onsubmit="return confirm('WARNING: This will delete the ENTIRE BATCH and ALL associated items. This action cannot be undone. Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                    Delete Batch
                </button>
            </form>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $batch->supplier?->name ?? 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Stone</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $batch->stone?->name ?? 'N/A' }}
                        <span class="text-gray-500">
                            ({{ $batch->stone?->color ?? 'N/A' }}
                            {{ $batch->stone?->size ? '- ' . $batch->stone->size : '' }})
                        </span>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Arrival Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $batch->arrival_date }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <h3 class="text-lg font-bold mb-4">Slabs in this Batch</h3>
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slab #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dimensions
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SQFT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $groupedItems = $batch->items->groupBy(function ($item) {
                        return number_format($item->width_in, 2) . '|' . number_format($item->length_in, 2) . '|' . $item->status;
                    });
                @endphp

                @foreach($groupedItems as $group)
                    @php
                        $first = $group->first();
                        $count = $group->count();
                        $slabNumbers = $group->pluck('slab_number')->sort()->implode(', ');
                        // Truncate slab numbers if too long
                        if (strlen($slabNumbers) > 50) {
                            $slabNumbers = substr($slabNumbers, 0, 50) . '...';
                        }
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            @if($count > 1)
                                <span class="font-bold text-indigo-600">Qty: {{ $count }}</span>
                                <div class="text-xs text-gray-500 mt-1" title="{{ $group->pluck('slab_number')->implode(', ') }}">
                                    Slabs: {{ $slabNumbers }}
                                </div>
                            @else
                                {{ $first->slab_number }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($first->width_in, 2) }}" x {{ number_format($first->length_in, 2) }}"
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($group->sum('sqft'), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span
                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $first->status === 'available' ? 'bg-green-50 text-green-700 ring-green-600/20' : ($first->status === 'reserved' ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20' : 'bg-red-50 text-red-700 ring-red-600/20') }}">
                                {{ ucfirst($first->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($count === 1)
                                <a href="{{ url('/items/' . $first->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400">Grouped</span>
                                    <form action="{{ route('items.bulk_destroy') }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete all {{ $count }} items in this group?');">
                                        @csrf
                                        @method('DELETE')
                                        @foreach($group as $item)
                                            <input type="hidden" name="ids[]" value="{{ $item->id }}">
                                        @endforeach
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-semibold">Delete
                                            Group</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection