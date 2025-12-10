@extends('layouts.app')

@section('content')
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">Inventory</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all individual slabs and items in your inventory including their
                dimensions, location, and status.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="{{ url('/items/create') }}"
                class="block rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Add New Item
            </a>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <form action="{{ route('items.bulk_destroy') }}" method="POST" id="bulk-delete-form">
                        @csrf
                        @method('DELETE')

                        <!-- Bulk Actions Bar -->
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 sm:px-6 flex items-center justify-between"
                            x-data="{ count: 0 }"
                            x-init="$watch('count', value => { if(value > 0) $el.classList.remove('hidden'); else $el.classList.add('hidden'); })"
                            class="hidden" id="bulk-actions-bar">
                            <div class="text-sm text-gray-700">
                                <span class="font-medium" id="selected-count">0</span> items selected
                            </div>
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to delete the selected items?')"
                                class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                Delete Selected
                            </button>
                        </div>

                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="relative px-7 sm:w-12 sm:px-6">
                                        <input type="checkbox" id="select-all"
                                            class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Item
                                        Details</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Dimensions
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Lot #
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Location
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status
                                    </th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($items as $item)
                                    <tr>
                                        <td class="relative px-7 sm:w-12 sm:px-6">
                                            @if($item->status === 'available')
                                                <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                                    class="item-checkbox absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                                    <i class="fas fa-cube"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium text-gray-900">{{ $item->stone->name }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $item->stone->color ?? 'N/A' }} • {{ $item->stone->size ?? 'N/A' }}
                                                    </div>
                                                    @if($item->quantity > 1)
                                                        <div class="text-sm font-bold text-indigo-600 mt-1">Qty:
                                                            {{ $item->quantity }}</div>
                                                    @else
                                                        <div class="text-gray-500">{{ $item->barcode }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="text-gray-900">{{ number_format($item->width_in, 2) }}" x
                                                {{ number_format($item->length_in, 2) }}"</div>
                                            <div class="text-gray-500">{{ number_format($item->sqft, 2) }} sqft</div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if($item->batch)
                                                <a href="{{ route('lots.show', $item->batch) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                                    {{ $item->batch->block_number }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">No Lot</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $item->location ?? 'N/A' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $item->status === 'available' ? 'bg-green-50 text-green-700 ring-green-600/20' : ($item->status === 'reserved' ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20' : 'bg-red-50 text-red-700 ring-red-600/20') }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td
                                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            @if($item->quantity > 1)
                                                <a href="{{ route('items.group', $item) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">View Group<span class="sr-only">,
                                                        {{ $item->stone->name }}</span></a>
                                            @else
                                                <a href="{{ route('items.show', $item) }}"
                                                    class="text-primary-600 hover:text-primary-900 mr-4">View<span class="sr-only">,
                                                        {{ $item->barcode }}</span></a>
                                            @endif
                                            <a href="{{ route('items.edit', $item) }}"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4">Edit<span class="sr-only">,
                                                    {{ $item->barcode }}</span></a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-4 px-4 sm:px-6 lg:px-8">
            {{ $items->links() }}
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const bulkActionsBar = document.getElementById('bulk-actions-bar');
            const selectedCountSpan = document.getElementById('selected-count');

            function updateBulkActions() {
                const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
                selectedCountSpan.textContent = selectedCount;
                if (selectedCount > 0) {
                    bulkActionsBar.classList.remove('hidden');
                } else {
                    bulkActionsBar.classList.add('hidden');
                }
            }

            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                updateBulkActions();
            });

            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkActions();
                    // Uncheck "Select All" if any item is unchecked
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    }
                });
            });
        });
    </script>
@endsection