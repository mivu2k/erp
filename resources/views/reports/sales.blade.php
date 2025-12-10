@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Sales
                    Report</h2>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <button onclick="window.print()" type="button"
                    class="ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Print</button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow sm:rounded-lg p-4">
            <form action="{{ route('reports.sales') }}" method="GET" class="flex gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium leading-6 text-gray-900">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                        class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium leading-6 text-gray-900">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                        class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Filter</button>
                @if(request('start_date'))
                    <a href="{{ route('reports.sales') }}" class="text-sm text-gray-600 hover:text-gray-900">Clear</a>
                @endif
            </form>
        </div>

        <!-- Summary -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        Total Revenue for selected period: <span
                            class="font-bold">{{ $currency_symbol }}{{ number_format($totalRevenue, 2) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Date
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Order #</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer</th>
                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($orders as $order)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 sm:pl-6">{{ $order->date }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->order_number }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->customer->name }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-medium text-gray-900">
                                {{ $currency_symbol }}{{ number_format($order->total_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">No orders found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection