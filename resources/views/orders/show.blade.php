@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Order #{{ $order->order_number }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <i class="fas fa-user mr-1.5 text-gray-400"></i>
                        {{ $order->customer->name }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1.5 text-gray-400"></i>
                        {{ $order->created_at->format('M d, Y') }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'production' ? 'bg-orange-100 text-orange-800' : ($order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($order->status) }}
                        </span>

                        @if($order->status !== 'completed')
                            <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="ml-4">
                                @csrf
                                @method('PATCH')
                                @if($order->status === 'quote')
                                    <button type="submit" name="status" value="confirmed"
                                        class="inline-flex items-center rounded bg-blue-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                        Confirm Order
                                    </button>
                                @elseif($order->status === 'confirmed')
                                    <button type="submit" name="status" value="production"
                                        class="inline-flex items-center rounded bg-orange-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                                        Start Production
                                    </button>
                                @elseif($order->status === 'production')
                                    <button type="submit" name="status" value="completed"
                                        class="inline-flex items-center rounded bg-green-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                        Complete Order
                                    </button>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('orders.invoice', $order) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-print mr-2"></i> Print Invoice
                </a>
                <a href="{{ route('orders.edit', $order) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Edit Order
                </a>
                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        onclick="return confirm('Are you sure you want to delete this order? This will release allocated items.')">
                        Delete Order
                    </button>
                </form>
                <a href="{{ route('orders.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Back to List
                </a>
            </div>
        </div>

        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Information</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Salesperson</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->name }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Delivery Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'Not set' }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->notes ?? 'None' }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $currency_symbol }}{{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Allocated Items</h3>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Material</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Block / Slab #</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dimensions (Net)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SqFt (Net)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Wastage</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $groupedOrderItems = $order->items->groupBy(function ($item) {
                                            return $item->item->stone_id . '|' . number_format($item->width_in, 2) . '|' . number_format($item->length_in, 2);
                                        });
                                    @endphp

                                    @foreach ($groupedOrderItems as $group)
                                        @php
                                            $first = $group->first();
                                            $count = $group->count();
                                            $slabNumbers = $group->map(fn($i) => '#' . $i->item->slab_number)->implode(', ');
                                            if (strlen($slabNumbers) > 50) {
                                                $slabNumbers = substr($slabNumbers, 0, 50) . '...';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $first->item->stone->name }}
                                                    <span class="text-xs text-gray-500 block">
                                                        {{ $first->item->stone->color }} {{ $first->item->stone->size ? '- ' . $first->item->stone->size : '' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($count > 1)
                                                        <span class="font-bold text-indigo-600">Qty: {{ $count }}</span>
                                                        <div class="text-xs text-gray-500 mt-1" title="{{ $group->map(fn($i) => '#' . $i->item->slab_number)->implode(', ') }}">
                                                            Slabs: {{ $slabNumbers }}
                                                        </div>
                                                    @else
                                                        {{ $first->item->batch->block_number ?? 'No Lot' }} /
                                                        #{{ $first->item->slab_number }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ number_format($first->width_in, 2) }}" x
                                                    {{ number_format($first->length_in, 2) }}"</div>
                                                <div class="text-xs text-gray-400">Gross: {{ number_format($first->item->width_in, 2) }}x{{ number_format($first->item->length_in, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($group->sum('sqft'), 2) }} sqft</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-red-500">{{ number_format($group->sum('wastage'), 2) }} sqft</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $currency_symbol }}{{ number_format($group->sum('price'), 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection