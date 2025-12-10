@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Reports
                    Dashboard</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Orders</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalOrders) }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Revenue</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    {{ $currency_symbol }}{{ number_format($totalRevenue, 2) }}
                </dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Est. Inventory Value</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    {{ $currency_symbol }}{{ number_format($totalInventoryValue, 2) }}
                </dd>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <a href="{{ route('reports.sales') }}"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Sales Report</h5>
                <p class="font-normal text-gray-700">View detailed sales history, filter by date range, and analyze revenue.
                </p>
            </a>
            <a href="{{ route('reports.inventory') }}"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Inventory Report</h5>
                <p class="font-normal text-gray-700">View current stock levels, value by item type, and average costs.</p>
            </a>
            <a href="{{ route('reports.qrcodes') }}"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Print Item QR Codes</h5>
                <p class="font-normal text-gray-700">Generate and print QR codes for all available inventory items.</p>
            </a>
            <a href="{{ route('reports.qrcodes.stones') }}"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Print Material QR Codes</h5>
                <p class="font-normal text-gray-700">Generate and print QR codes for all Material definitions (Stones).</p>
            </a>
        </div>
    </div>
@endsection