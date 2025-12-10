@extends('layouts.app')

@section('content')
    <div>
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Edit Lot</h2>
            </div>
        </div>

        <form action="{{ route('lots.update', $batch) }}" method="POST" class="space-y-8 divide-y divide-gray-200">
            @csrf
            @method('PUT')
            <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
                <div class="space-y-6 sm:space-y-5">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Lot Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Update details about the lot.</p>
                    </div>

                    <div class="space-y-6 sm:space-y-5">
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="supplier_id" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Supplier</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <select id="supplier_id" name="supplier_id" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $batch->supplier_id == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="stone_id" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Material</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <select id="stone_id" name="stone_id" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    @foreach($stones as $stone)
                                        <option value="{{ $stone->id }}" {{ $batch->stone_id == $stone->id ? 'selected' : '' }}>
                                            {{ $stone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="block_number" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Lot Number</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="block_number" id="block_number" value="{{ $batch->block_number }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="arrival_date" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Arrival Date</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="date" name="arrival_date" id="arrival_date" value="{{ $batch->arrival_date }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="cost_price" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Cost Price</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ $batch->cost_price }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end gap-x-3">
                    <a href="{{ route('lots.index') }}"
                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Update Lot</button>
                </div>
            </div>
        </form>
    </div>
@endsection
