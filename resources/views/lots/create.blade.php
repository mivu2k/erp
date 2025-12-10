@extends('layouts.app')

@section('content')
    <div x-data="batchForm()">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Receive New Lot
                </h2>
                <p class="mt-1 text-sm text-gray-500">Enter supplier details and slab dimensions.</p>
            </div>
        </div>

        <form action="{{ route('lots.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Lot Details Card -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Lot Information</h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="supplier_id"
                                class="block text-sm font-medium leading-6 text-gray-900">Supplier</label>
                            <div class="mt-2">
                                <select id="supplier_id" name="supplier_id" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="stone_id" class="block text-sm font-medium leading-6 text-gray-900">Item</label>
                            <div class="mt-2">
                                <select id="stone_id" name="stone_id" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="">Select Item</option>
                                    @foreach($stones as $stone)
                                        <option value="{{ $stone->id }}">{{ $stone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="block_number" class="block text-sm font-medium leading-6 text-gray-900">Lot
                                Number</label>
                            <div class="mt-2">
                                <input type="text" name="block_number" id="block_number" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="arrival_date" class="block text-sm font-medium leading-6 text-gray-900">Arrival
                                Date</label>
                            <div class="mt-2">
                                <input type="date" name="arrival_date" id="arrival_date" value="{{ date('Y-m-d') }}"
                                    required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="cost_price" class="block text-sm font-medium leading-6 text-gray-900">Total Cost
                                Price</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" name="cost_price" id="cost_price"
                                    class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Measurement Sheet Card -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <div class="sm:flex sm:items-center sm:justify-between mb-4">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Slab Dimensions</h3>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-end gap-4 mb-6">
                        <div class="flex-1 max-w-xs">
                            <label for="add_rows_count" class="block text-sm font-medium leading-6 text-gray-900">Rows to
                                Add</label>
                            <div class="mt-2">
                                <input type="number" id="add_rows_count" x-model="rowsToAdd" min="1" max="100"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                        <button type="button" @click="addRow()"
                            class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Add Rows
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="mt-4 flow-root">
                        <div class="overflow-x-auto min-w-full">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                    Quantity</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Width
                                                    (in)</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Length
                                                    (in)</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Thickness (mm)</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total
                                                    SQFT</th>
                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            <template x-for="(row, index) in rows" :key="index">
                                                <tr>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                                        <input type="number" :name="'slabs['+index+'][quantity]'"
                                                            x-model="row.quantity" required
                                                            class="block w-20 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        <input type="number" step="0.01"
                                                            :name="'slabs['+index+'][width_in]'" x-model="row.width"
                                                            required
                                                            class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        <input type="number" step="0.01"
                                                            :name="'slabs['+index+'][length_in]'" x-model="row.length"
                                                            required
                                                            class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        <input type="number" step="0.01"
                                                            :name="'slabs['+index+'][thickness_mm]'" x-model="row.thickness"
                                                            required
                                                            class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-medium"
                                                        x-text="calculateSqft(row)"></td>
                                                    <td
                                                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                        <button type="button" @click="removeRow(index)"
                                                            class="text-red-600 hover:text-red-900"
                                                            x-show="rows.length > 1">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="rounded-md bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    Save Lot
                </button>
            </div>
        </form>
    </div>

    <script>
        function batchForm() {
            return {
                rowsToAdd: 1,
                rows: [
                    { quantity: 1, width: 0, length: 0, thickness: 20 }
                ],
                addRow() {
                    for (let i = 0; i < this.rowsToAdd; i++) {
                        this.rows.push({ quantity: 1, width: 0, length: 0, thickness: 20 });
                    }
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                },
                calculateSqft(row) {
                    if (row.width && row.length && row.quantity) {
                        const sqftPerSlab = (row.width * row.length) / 144;
                        return (sqftPerSlab * row.quantity).toFixed(2);
                    }
                    return '0.00';
                }
            }
        }
    </script>
@endsection