@php
    $navItems = [
        ['label' => 'Scan', 'url' => url('/scan'), 'icon' => 'fa-qrcode', 'active' => request()->is('scan')],
        ['label' => 'Dashboard', 'url' => url('/dashboard'), 'icon' => 'fa-home', 'active' => request()->is('dashboard')],
        ['label' => 'Inventory', 'url' => url('/items'), 'icon' => 'fa-cubes', 'active' => request()->is('items*')],
        ['label' => 'Materials', 'url' => route('materials.index'), 'icon' => 'fa-tags', 'active' => request()->routeIs('materials.*')],
        ['label' => 'Lots', 'url' => route('lots.index'), 'icon' => 'fa-layer-group', 'active' => request()->routeIs('lots.*')],
        ['label' => 'Suppliers', 'url' => route('suppliers.index'), 'icon' => 'fa-truck', 'active' => request()->routeIs('suppliers.*')],
        ['label' => 'Fabrication', 'url' => route('work_orders.index'), 'icon' => 'fa-hammer', 'active' => request()->routeIs('work_orders.*')],
        ['label' => 'Customers', 'url' => route('customers.index'), 'icon' => 'fa-users', 'active' => request()->routeIs('customers.*')],
        ['label' => 'Orders', 'url' => route('orders.index'), 'icon' => 'fa-shopping-cart', 'active' => request()->routeIs('orders.*')],
        ['label' => 'Reports', 'url' => route('reports.index'), 'icon' => 'fa-chart-line', 'active' => request()->routeIs('reports.*')],
    ];
@endphp

@foreach($navItems as $item)
    <li>
        <a href="{{ $item['url'] }}"
            class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 {{ $item['active'] ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
            <div
                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border {{ $item['active'] ? 'border-primary-600 bg-white' : 'border-gray-200 bg-white group-hover:border-primary-600 group-hover:text-primary-600' }}">
                <i
                    class="fas {{ $item['icon'] }} {{ $item['active'] ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} text-[0.625rem]"></i>
            </div>
            {{ $item['label'] }}
        </a>
    </li>
@endforeach