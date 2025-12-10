@extends('layouts.app')

@section('content')
    <div>
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Edit Item
                </h2>
            </div>
        </div>

        <form action="{{ route('items.update', $item) }}" method="POST" class="mt-8 space-y-8 divide-y divide-gray-200">
            @csrf
            @method('PUT')
            <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
                <div class="space-y-6 sm:space-y-5">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Item Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Update details about the inventory item.</p>
                    </div>

                    <div class="space-y-6 sm:space-y-5">
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="stone_id"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Material</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <select id="stone_id" name="stone_id" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    @foreach($stones as $stone)
                                        <option value="{{ $stone->id }}" {{ $item->stone_id == $stone->id ? 'selected' : '' }}>
                                            {{ $stone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="width_in" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Width
                                (in)</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="number" step="0.01" name="width_in" id="width_in" value="{{ $item->width_in }}"
                                    required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="length_in"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Length (in)</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="number" step="0.01" name="length_in" id="length_in"
                                    value="{{ $item->length_in }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="thickness_mm"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Thickness (mm)</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="number" step="0.01" name="thickness_mm" id="thickness_mm"
                                    value="{{ $item->thickness_mm }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="quantity_pieces"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Quantity
                                (Pieces)</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="number" name="quantity_pieces" id="quantity_pieces"
                                    value="{{ $item->quantity_pieces }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="price_per_sqft"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Price per SQFT</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="number" step="0.01" name="price_per_sqft" id="price_per_sqft"
                                    value="{{ $item->price_per_sqft }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="location"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Location</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="location" id="location" value="{{ $item->location }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="status"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Status</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <select id="status" name="status" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    <option value="available" {{ $item->status == 'available' ? 'selected' : '' }}>Available
                                    </option>
                                    <option value="reserved" {{ $item->status == 'reserved' ? 'selected' : '' }}>Reserved
                                    </option>
                                    <option value="sold" {{ $item->status == 'sold' ? 'selected' : '' }}>Sold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end gap-x-3">
                    <a href="{{ route('items.index') }}"
                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Update
                        Item</button>
                </div>
            </div>
        </form>
    </div>
@endsection