@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Inventory
                    Report</h2>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <button onclick="window.print()" type="button"
                    class="ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Print</button>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        Total Estimated Inventory Value: <span class="font-bold">{{ $currency_symbol }}{{ number_format($totalValue, 2) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Item
                            Name</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Slabs Count</th>
                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Total SqFt</th>
                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Avg Price</th>
                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Est. Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($inventoryData as $data)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {{ $data['name'] }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $data['type'] }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-500">{{ $data['count'] }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-500">
                                {{ number_format($data['sqft'], 2) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-500">
                                {{ $currency_symbol }}{{ number_format($data['avg_price'], 2) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-medium text-gray-900">
                                {{ $currency_symbol }}{{ number_format($data['value'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection