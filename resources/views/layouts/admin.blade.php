<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StoneERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'block' : 'hidden'" @click.away="sidebarOpen = false"
            class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

        <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
            class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center mt-8">
                <div class="flex items-center">
                    <span class="text-2xl font-semibold text-white">StoneERP</span>
                </div>
            </div>

            <nav class="mt-10">
                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-gray-700 bg-opacity-25"
                    href="{{ url('/dashboard') }}">
                    <i class="fas fa-tachometer-alt w-6 h-6"></i>
                    <span class="mx-3">Dashboard</span>
                </a>

                <p class="px-6 mt-6 text-xs font-bold text-gray-400 uppercase">Inventory</p>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="{{ route('batches.index') }}">
                    <i class="fas fa-cubes w-6 h-6"></i>
                    <span class="mx-3">Batches (Receiving)</span>
                </a>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="{{ url('/items') }}">
                    <i class="fas fa-th w-6 h-6"></i>
                    <span class="mx-3">Stock (Slabs)</span>
                </a>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="{{ url('/scan') }}">
                    <i class="fas fa-qrcode w-6 h-6"></i>
                    <span class="mx-3">Scan / Lookup</span>
                </a>

                <p class="px-6 mt-6 text-xs font-bold text-gray-400 uppercase">Sales</p>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="#">
                    <i class="fas fa-shopping-cart w-6 h-6"></i>
                    <span class="mx-3">Orders</span>
                </a>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="#">
                    <i class="fas fa-users w-6 h-6"></i>
                    <span class="mx-3">Customers</span>
                </a>

                <p class="px-6 mt-6 text-xs font-bold text-gray-400 uppercase">Purchasing</p>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="{{ route('suppliers.index') }}">
                    <i class="fas fa-truck w-6 h-6"></i>
                    <span class="mx-3">Suppliers</span>
                </a>

                <p class="px-6 mt-6 text-xs font-bold text-gray-400 uppercase">Configuration</p>
                <a class="flex items-center px-6 py-2 mt-2 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                    href="{{ route('materials.index') }}">
                    <i class="fas fa-layer-group w-6 h-6"></i>
                    <span class="mx-3">Categories</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-indigo-600">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <i class="fas fa-bars w-6 h-6"></i>
                    </button>
                    <div class="relative mx-4 lg:mx-0">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-500"></i>
                        </span>
                        <input
                            class="w-32 pl-10 pr-4 text-indigo-600 border-gray-200 rounded-md sm:w-64 focus:border-indigo-600 focus:ring focus:ring-indigo-600 focus:ring-opacity-40"
                            type="text" placeholder="Search">
                    </div>
                </div>

                <div class="flex items-center">
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                            <img class="object-cover w-full h-full"
                                src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}"
                                alt="Your avatar">
                        </button>

                        <div x-show="dropdownOpen" @click="dropdownOpen = false"
                            class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                        <div x-show="dropdownOpen"
                            class="absolute right-0 z-20 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                            style="display: none;">
                            <a href="{{ route('materials.index') }}"
                                class="{{ request()->routeIs('materials.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i
                                    class="fas fa-cubes mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-300"></i>
                                Materials
                            </a>

                            <a href="{{ route('lots.index') }}"
                                class="{{ request()->routeIs('lots.*') ? 'bg-indigo-700 text-white' : 'text-indigo-100 hover:bg-indigo-600' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-layer-group mr-3 flex-shrink-0 h-6 w-6"></i>
                                Lots
                            </a>

                            <a href="{{ route('work_orders.index') }}"
                                class="{{ request()->routeIs('work_orders.*') ? 'bg-indigo-700 text-white' : 'text-indigo-100 hover:bg-indigo-600' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-hammer mr-3 flex-shrink-0 h-6 w-6"></i>
                                Fabrication
                            </a>
                        </div>
                    </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>