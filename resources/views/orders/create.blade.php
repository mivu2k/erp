@extends('layouts.app')

@section('content')
    <div x-data="orderForm()">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Create New Order
                </h2>
                <p class="mt-1 text-sm text-gray-500">Select items from inventory and create an order.</p>
            </div>
        </div>

        <form action="{{ route('orders.store') }}" method="POST" class="space-y-8">
            @csrf

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
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="date" class="block text-sm font-medium leading-6 text-gray-900">Order Date</label>
                            <div class="mt-2">
                                <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="vehicle_no" class="block text-sm font-medium leading-6 text-gray-900">Vehicle
                                Number</label>
                            <div class="mt-2">
                                <input type="text" name="vehicle_no" id="vehicle_no" placeholder="Optional"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Selection -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Select Items</h3>
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
                                    <template x-for="group in groupedBatchItems" :key="group.id">
                                        <tr :class="{'bg-indigo-50': isGroupSelected(group)}">
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <input type="checkbox" @click="toggleGroup(group)"
                                                    :checked="isGroupSelected(group)"
                                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                <div x-show="group.count > 1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="font-bold text-indigo-600">Qty:</span>
                                                        <!-- Partial Selection Input -->
                                                        <input type="number"
                                                            :value="getGroupSelectedCount(group) > 0 ? getGroupSelectedCount(group) : group.count"
                                                            @input="updateGroupQty(group, $event.target.value)"
                                                            class="block w-20 rounded-md border-0 py-1 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                            min="0" :max="group.count">
                                                        <span class="text-gray-500" x-text="'/ ' + group.count"></span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1"
                                                        x-text="'Slabs: ' + (group.slab_numbers.length > 50 ? group.slab_numbers.substring(0, 50) + '...' : group.slab_numbers)"
                                                        :title="group.slab_numbers"></div>
                                                </div>
                                                <div x-show="group.count === 1" x-text="group.slab_numbers"></div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                                x-text="parseFloat(group.length_in).toFixed(2) + ' x ' + parseFloat(group.width_in).toFixed(2) + ' in'">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                                x-text="parseFloat(group.total_sqft).toFixed(2)"></td>
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
                            <span x-show="showSuccess" x-transition.opacity
                                class="ml-3 text-sm text-green-600 font-medium">Items Added!</span>
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
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Slab
                                        #</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gross
                                        Dims</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Ordered
                                        Length (in)</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Ordered
                                        Width (in)</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Ordered
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
                                            <div x-show="row.is_group">
                                                <span class="font-bold text-indigo-600" x-text="'Qty: ' + row.count"></span>
                                                <div class="text-xs text-gray-500 mt-1" x-text="row.slab_display"
                                                    :title="row.slab_numbers"></div>
                                            </div>
                                            <div x-show="!row.is_group" x-text="row.slab_numbers"></div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span
                                                x-text="row.gross_length.toFixed(2) + ' x ' + row.gross_width.toFixed(2)"></span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <input type="number" step="0.01" x-model="row.net_length" required
                                                class="block w-24 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <input type="number" step="0.01" x-model="row.net_width" required
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
                                                <input type="number" step="0.01" x-model="row.price" required
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

            <!-- Hidden Inputs Container -->
            <div class="hidden">
                <template x-for="(row, index) in orderItems" :key="index">
                    <div>
                        <!-- Array of Item IDs -->
                        <template x-for="item in row.items" :key="item.id">
                            <input type="hidden" :name="'items['+index+'][item_ids][]'" :value="item.id">
                        </template>

                        <!-- Single inputs for shared attributes -->
                        <input type="hidden" :name="'items['+index+'][thickness_mm]'" :value="row.thickness_mm">
                        <input type="hidden" :name="'items['+index+'][length_in]'" :value="row.net_length">
                        <input type="hidden" :name="'items['+index+'][width_in]'" :value="row.net_width">
                        <input type="hidden" :name="'items['+index+'][price]'" :value="row.price">
                    </div>
                </template>
            </div>

            <div class="flex justify-end">
                <button type="submit" x-show="orderItems.length > 0"
                    class="rounded-md bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    Create Order
                </button>
            </div>
        </form>
    </div>

    <script>
        function orderForm() {
            return {
                batches: @json($batches),
                selectedMaterial: '',
                selectedBatchId: '',
                selectedItemIds: [],
                showSuccess: false,
                orderItems: [],

                get filteredBatches() {
                    if (!this.selectedMaterial) return [];
                    return this.batches.filter(b => b.stone_id == this.selectedMaterial);
                },

                get currentBatchItems() {
                    if (!this.selectedBatchId) return [];
                    const batch = this.batches.find(b => b.id == this.selectedBatchId);
                    return batch ? batch.items : [];
                },

                get groupedBatchItems() {
                    const items = this.currentBatchItems;
                    const groups = {};

                    items.forEach(item => {
                        const key = parseFloat(item.length_in).toFixed(2) + '|' + parseFloat(item.width_in).toFixed(2);
                        if (!groups[key]) {
                            groups[key] = {
                                key: key,
                                items: [],
                                length_in: item.length_in,
                                width_in: item.width_in,
                                total_sqft: 0
                            };
                        }
                        groups[key].items.push(item);
                        groups[key].total_sqft += parseFloat(item.sqft);
                    });

                    return Object.values(groups).map(group => ({
                        ...group,
                        id: 'group_' + group.key, // Unique ID for the group row
                        count: group.items.length,
                        slab_numbers: group.items.map(i => i.slab_number).join(', ')
                    }));
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

                isGroupSelected(group) {
                    // A group is considered selected if AT LEAST ONE item is selected
                    // But for the checkbox state:
                    // - Checked if ALL are selected (or matches selectedQty?)
                    // - Indeterminate if some are selected?
                    // Let's simplify: Checked if count > 0.
                    return group.items.some(item => this.selectedItemIds.includes(item.id));
                },

                getGroupSelectedCount(group) {
                    return group.items.filter(item => this.selectedItemIds.includes(item.id)).length;
                },

                updateGroupQty(group, qty) {
                    let newQty = parseInt(qty);
                    if (isNaN(newQty) || newQty < 0) newQty = 0;
                    if (newQty > group.items.length) newQty = group.items.length;

                    // 1. Remove all items of this group from selectedItemIds
                    this.selectedItemIds = this.selectedItemIds.filter(id => !group.items.some(i => i.id == id));

                    // 2. Add the first 'newQty' items
                    const idsToAdd = group.items.slice(0, newQty).map(i => i.id);
                    this.selectedItemIds = [...this.selectedItemIds, ...idsToAdd];
                },

                toggleGroup(group) {
                    const currentCount = this.getGroupSelectedCount(group);
                    if (currentCount === group.items.length) {
                        // Deselect all
                        this.updateGroupQty(group, 0);
                    } else {
                        // Select all
                        this.updateGroupQty(group, group.items.length);
                    }
                },

                addSelectedItems() {
                    // 1. Get all selected items
                    const itemsToAdd = this.currentBatchItems.filter(i => this.selectedItemIds.some(id => id == i.id));

                    if (itemsToAdd.length === 0) return;

                    // 2. Group them by dimensions (same logic as groupedBatchItems)
                    const groups = {};
                    itemsToAdd.forEach(item => {
                        const key = parseFloat(item.length_in).toFixed(2) + '|' + parseFloat(item.width_in).toFixed(2);
                        if (!groups[key]) {
                            groups[key] = {
                                items: [],
                                length_in: item.length_in,
                                width_in: item.width_in,
                                thickness_mm: item.thickness_mm
                            };
                        }
                        groups[key].items.push(item);
                    });

                    // 3. Create Order Rows (Grouped or Single)
                    Object.values(groups).forEach(group => {
                        // Filter out empty slab numbers for cleaner display
                        const validSlabNumbers = group.items.map(i => i.slab_number).filter(n => n != null && String(n).trim() !== '');
                        let slabDisplay = '';

                        if (validSlabNumbers.length === 0) {
                            slabDisplay = 'Unnumbered Slabs';
                        } else if (group.items.length > 1) {
                            slabDisplay = group.items.length + ' Slabs Selected';
                        } else {
                            slabDisplay = validSlabNumbers[0];
                        }

                        this.orderItems.push({
                            is_group: group.items.length > 1,
                            items: group.items, // Array of actual items
                            count: group.items.length,
                            slab_numbers: validSlabNumbers.join(', '), // Keep full list for tooltip
                            slab_display: slabDisplay, // Clean display text
                            gross_length: parseFloat(group.length_in),
                            gross_width: parseFloat(group.width_in),
                            thickness_mm: parseFloat(group.thickness_mm),
                            net_length: parseFloat(group.length_in),
                            net_width: parseFloat(group.width_in),
                            price: 0
                        });
                    });

                    // Clear selection
                    this.selectedItemIds = [];
                    this.showSuccess = true;
                    console.log('Items added. OrderItems:', JSON.parse(JSON.stringify(this.orderItems)));
                    setTimeout(() => this.showSuccess = false, 2000);
                },

                removeOrderItem(index) {
                    this.orderItems.splice(index, 1);
                },

                calculateNetSqFt(row) {
                    const unitSqFt = (row.net_length * row.net_width) / 144;
                    return (unitSqFt * row.count).toFixed(2);
                },

                calculateWastage(row) {
                    const grossSqFt = (row.gross_length * row.gross_width) / 144;
                    const netSqFt = (row.net_length * row.net_width) / 144;
                    const unitWastage = grossSqFt - netSqFt;
                    const totalWastage = unitWastage * row.count;
                    return totalWastage > 0 ? totalWastage.toFixed(2) : '0.00';
                },

                calculateRowTotal(row) {
                    const unitSqFt = (row.gross_length * row.gross_width) / 144;
                    const total = unitSqFt * row.price * row.count;
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
                        const unitSqFt = (row.gross_length * row.gross_width) / 144;
                        return sum + (unitSqFt * row.price * row.count);
                    }, 0).toFixed(2);
                }
            }
        }
    </script>
@endsection