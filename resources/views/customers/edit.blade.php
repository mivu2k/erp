@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Edit Customer: {{ $customer->name }}
                </h2>
            </div>
        </div>

        <div class="mt-8">
            <form action="{{ route('customers.update', $customer) }}" method="POST"
                class="space-y-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <div class="mt-1">
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <div class="mt-1">
                            <textarea id="address" name="address" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 sm:text-sm border border-gray-300 rounded-md">{{ old('address', $customer->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button"
                        onclick="if(confirm('Are you sure?')) document.getElementById('delete-form').submit();"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Customer
                    </button>
                    <div class="flex">
                        <a href="{{ route('customers.index') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update
                        </button>
                    </div>
                </div>
            </form>
            <form id="delete-form" action="{{ route('customers.destroy', $customer) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection