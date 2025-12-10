@extends('layouts.app')

@section('content')
    <div x-data="orderEditForm()">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Edit Order #{{ $order->order_number }}
                </h2>
            </div>
        </div>

        <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Order Details -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Order Details</h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <label for="customer_id"
                                class="block text-sm font-medium leading-6 text-gray-900">Customer</label>
                            <div class="mt-2">
                                <select id="customer_id" name="customer_id" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                            <div class="mt-2">
                                <select id="status" name="status" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="quote" {{ $order->status == 'quote' ? 'selected' : '' }}>Quote</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed
                                    </option>
                                    <option value="production" {{ $order->status == 'production' ? 'selected' : '' }}>
                                        Production</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="delivery_date" class="block text-sm font-medium leading-6 text-gray-900">Delivery
                                Date</label>
                            <div class="mt-2">
                                <input type="date" name="delivery_date" id="delivery_date"
                                    value="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notes</label>
                            <div class="mt-2">
                                <textarea id="notes" name="notes" rows="2"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ $order->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Selection -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Add Items</h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mb-6">
                        <div class="sm:col-span-3">
                            <label for="material_id"
                                class="block text-sm font-medium leading-6 text-gray-900">Material</label>
                            <div class="mt-2">
                                <select id="material_id" x-model="selectedMaterial"
                                    @change="selectedBatchId = ''; selectedItemIds = []"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="">Select Material</option>
                                    @foreach ($stones as $stone)
                                        <option value="{{ $stone->id }}">{{ $stone->name }} ({{ $stone->color }}
                                            {{ $stone->size }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="batch_id" class="block text-sm font-medium leading-6 text-gray-900">Lot</label>
                            <div class="mt-2">
                                <select id="batch_id" x-model="selectedBatchId"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="">Select Lot</option>
                                    <template x-for="batch in filteredBatches" :key="batch.id">
                                        <option :value="batch.id"
                                            x-text="batch.block_number + ' (' + batch.items.length + ' items)'"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Available Items List -->
                    <div x-show="selectedBatchId" class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Available Slabs in Lot</h4>
                        <div class="overflow-x-auto border rounded-md max-h-64 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th scope="col"
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" @click="toggleAllBatchItems()"
                                                :checked="allBatchItemsSelected"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Slab #</th>
                                        <th scope="col"
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dimensions</th>
                                        <th scope="col"
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sq.Ft</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="item in currentBatchItems" :key="item.id">
                                        <tr :class="{'bg-indigo-50': isItemSelected(item.id)}">
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <input type="checkbox" :value="item.id" x-model="selectedItemIds"
                                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900"
                                                x-text="item.slab_number"></td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                                x-text="parseFloat(item.length_in).toFixed(2) + ' x ' + parseFloat(item.width_in).toFixed(2) + ' in'">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                                x-text="parseFloat(item.total_sqft).toFixed(2)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <button type="button" @click="addSelectedItems()"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Add Selected Items
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Table -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden" x-show="orderItems.length > 0">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Order Items</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Item
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gross
                                        Dims</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Net
                                        Length (in)</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Net
                                        Width (in)</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Net
                                        Sq.Ft</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Wastage</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Price/SqFt</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total
                                    </th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span
                                            class="sr-only">Remove</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(row, index) in orderItems" :key="index">
                                    <tr>
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            <span x-text="row.name"></span><br>
                                            <span class="text-xs text-gray-500" x-text="row.details"></span>
                                            <input type="hidden" :name="'items['+index+'][item_id]'" :value="row.item_id">
                                            <input type="hidden" :name="'items['+index+'][thickness_mm]'"
                                                :value="row.thickness_mm">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span
                                                x-text="row.gross_length.toFixed(2) + ' x ' + row.gross_width.toFixed(2)"></span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <input type="number" step="0.01" x-model="row.net_length"
                                                :name="'items['+index+'][length_in]'" required
                                                class="block w-24 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <input type="number" step="0.01" x-model="row.net_width"
                                                :name="'items['+index+'][width_in]'" required
                                                class="block w-24 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-medium"
                                            x-text="calculateNetSqFt(row)"></td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-red-500"
                                            x-text="calculateWastage(row)"></td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="relative rounded-md shadow-sm">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 sm:text-sm">{{ $currency_symbol }}</span>
                                                </div>
                                                <input type="number" step="0.01" x-model="row.price"
                                                    :name="'items['+index+'][price]'" required
                                                    class="block w-24 rounded-md border-0 py-1.5 pl-7 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900"
                                            x-text="calculateRowTotal(row)"></td>
                                        <td
                                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <button type="button" @click="removeOrderItem(index)"
                                                class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="py-3.5 pl-4 pr-3 text-right text-sm font-semibold text-gray-900">
                                        Totals:</td>
                                    <td class="px-3 py-3.5 text-left text-sm font-bold text-gray-900"
                                        x-text="totalNetSqFt + ' Sq.Ft'"></td>
                                    <td class="px-3 py-3.5 text-left text-sm font-bold text-red-600"
                                        x-text="totalWastage + ' Sq.Ft'"></td>
                                    <td></td>
                                    <td class="px-3 py-3.5 text-left text-sm font-bold text-gray-900"
                                        x-text="'{{ $currency_symbol }}' + totalOrderAmount"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('orders.index') }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" x-show="orderItems.length > 0"
                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Order
                </button>
            </div>
        </form>
    </div>

    <script>
        function orderEditForm() {
            return {
                batches: @json($batches),
                selectedMaterial: '',
                selectedBatchId: '',
                selectedItemIds: [],
                orderItems: [
                    @foreach($order->items as $oi)
                                                                        {
                            item_id: {{ $oi->item_id }},
                            name: '{{ $oi->item->stone->name }}',
                            details: 'Lot: {{ $oi->item->batch->block_number ?? "N/A" }} / #{{ $oi->item->slab_number }}',
                            gross_length: {{ $oi->item->length_in }},
                            gross_width: {{ $oi->item->width_in }},
                            thickness_mm: {{ $oi->item->thickness_mm }},
                            net_length: {{ $oi->length_in }},
                            net_width: {{ $oi->width_in }},
                            price: parseFloat({{ $oi->price / ($oi->sqft > 0 ? $oi->sqft : 1) }}).toFixed(2)
                        },
                    @endforeach
                                        ],

                get filteredBatches() {
                    if (!this.selectedMaterial) return [];
                    return this.batches.filter(b => b.stone_id == this.selectedMaterial);
                },

                get currentBatchItems() {
                    if (!this.selectedBatchId) return [];
                    const batch = this.batches.find(b => b.id == this.selectedBatchId);
                    return batch ? batch.items : [];
                },

                get allBatchItemsSelected() {
                    return this.currentBatchItems.length > 0 && this.currentBatchItems.every(i => this.selectedItemIds.some(id => id == i.id));
                },

                toggleAllBatchItems() {
                    if (this.allBatchItemsSelected) {
                        this.selectedItemIds = [];
                    } else {
                        this.selectedItemIds = this.currentBatchItems.map(i => i.id);
                    }
                },

                isItemSelected(id) {
                    return this.selectedItemIds.some(selectedId => selectedId == id);
                },

                addSelectedItems() {
                    const itemsToAdd = this.currentBatchItems.filter(i => this.selectedItemIds.some(id => id == i.id));

                    itemsToAdd.forEach(item => {
                        // Allow duplicates
                        this.orderItems.push({
                            item_id: item.id,
                            name: item.stone.name,
                            details: 'Lot: ' + (item.batch.block_number || 'N/A') + ' / #' + item.slab_number,
                            gross_length: parseFloat(item.length_in),
                            gross_width: parseFloat(item.width_in),
                            thickness_mm: parseFloat(item.thickness_mm),
                            net_length: parseFloat(item.length_in),
                            net_width: parseFloat(item.width_in),
                            price: 0
                        });
                    });

                    this.selectedItemIds = [];
                },

                removeOrderItem(index) {
                    this.orderItems.splice(index, 1);
                },

                calculateNetSqFt(row) {
                    const sqft = (row.net_length * row.net_width) / 144;
                    return sqft.toFixed(2);
                },

                calculateWastage(row) {
                    const grossSqFt = (row.gross_length * row.gross_width) / 144;
                    const netSqFt = (row.net_length * row.net_width) / 144;
                    const wastage = grossSqFt - netSqFt;
                    return wastage > 0 ? wastage.toFixed(2) : '0.00';
                },

                calculateRowTotal(row) {
                    const netSqFt = (row.net_length * row.net_width) / 144;
                    const total = netSqFt * row.price;
                    return '{{ $currency_symbol }}' + total.toFixed(2);
                },

                get totalNetSqFt() {
                    return this.orderItems.reduce((sum, row) => sum + parseFloat(this.calculateNetSqFt(row)), 0).toFixed(2);
                },

                get totalWastage() {
                    return this.orderItems.reduce((sum, row) => sum + parseFloat(this.calculateWastage(row)), 0).toFixed(2);
                },

                get totalOrderAmount() {
                    return this.orderItems.reduce((sum, row) => {
                        const netSqFt = (row.net_length * row.net_width) / 144;
                        return sum + (netSqFt * row.price);
                    }, 0).toFixed(2);
                }
            }
        }
    </script>
@endsection