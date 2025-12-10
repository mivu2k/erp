@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Add New Inventory Item</h1>

        <form action="{{ url('/items') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="stone_id">
                    Stone
                </label>
                <select name="stone_id" id="stone_id"
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                    @foreach(\App\Models\Stone::all() as $stone)
                        <option value="{{ $stone->id }}">{{ $stone->name }} ({{ $stone->type }} - {{ $stone->color_finish }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-wrap -mx-3 mb-4">
                <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="width_in">
                        Width (inches)
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="width_in" name="width_in" type="number" step="0.01" required>
                </div>
                <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="length_in">
                        Length (inches)
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="length_in" name="length_in" type="number" step="0.01" required>
                </div>
                <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="thickness_mm">
                        Thickness (mm)
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="thickness_mm" name="thickness_mm" type="number" step="0.01">
                </div>
            </div>

            <div class="flex flex-wrap -mx-3 mb-4" x-data="{ bulkMode: false }">
                <div class="w-full px-3 mb-4">
                    <div class="flex items-center">
                        <input id="bulk_mode" name="bulk_mode" type="checkbox" x-model="bulkMode"
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        <label for="bulk_mode" class="ml-2 block text-sm text-gray-900 font-bold">
                            Bulk Creation Mode (Add Multiple Slabs)
                        </label>
                    </div>
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0" x-show="!bulkMode">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quantity_pieces">
                        Quantity (Pieces)
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="quantity_pieces" name="quantity_pieces" type="number" value="1" required>
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0" x-show="bulkMode" x-cloak>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bulk_count">
                        Number of Slabs
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="bulk_count" name="bulk_count" type="number" min="2" value="2">
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price_per_sqft">
                        Price per SQFT ({{ $currency_symbol }})
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="price_per_sqft" name="price_per_sqft" type="number" step="0.01" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                    Location
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="location" name="location" type="text" placeholder="e.g., Aisle 1, Rack B">
            </div>

            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Add Item
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                    href="{{ url('/items') }}">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection