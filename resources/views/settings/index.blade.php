@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Settings
                </h2>
            </div>
        </div>

        <!-- Company Information Section -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Company Information</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Update your company details to be displayed on invoices and reports.</p>
                </div>

                <form action="{{ route('settings.company.store') }}" method="POST" class="mt-5 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900">Company
                                Name</label>
                            <div class="mt-2">
                                <input type="text" name="company_name" id="company_name"
                                    value="{{ $settings['company_name'] ?? '' }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="company_address"
                                class="block text-sm font-medium leading-6 text-gray-900">Address</label>
                            <div class="mt-2">
                                <textarea name="company_address" id="company_address" rows="3"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $settings['company_address'] ?? '' }}</textarea>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Full address including City, State, ZIP.</p>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="company_email"
                                class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                            <div class="mt-2">
                                <input type="email" name="company_email" id="company_email"
                                    value="{{ $settings['company_email'] ?? '' }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="company_phone"
                                class="block text-sm font-medium leading-6 text-gray-900">Phone</label>
                            <div class="mt-2">
                                <input type="text" name="company_phone" id="company_phone"
                                    value="{{ $settings['company_phone'] ?? '' }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save
                            Information</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Currencies Section -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Currencies</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Manage available currencies. The default currency will be used for all new transactions.</p>
                </div>

                <div class="mt-5">
                    <form action="{{ route('settings.currency.store') }}" method="POST" class="flex gap-4 items-end">
                        @csrf
                        <div>
                            <label for="code" class="block text-sm font-medium leading-6 text-gray-900">Code</label>
                            <input type="text" name="code" id="code" placeholder="USD" required
                                class="block w-24 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div>
                            <label for="symbol" class="block text-sm font-medium leading-6 text-gray-900">Symbol</label>
                            <input type="text" name="symbol" id="symbol" placeholder="$" required
                                class="block w-24 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div>
                            <label for="rate" class="block text-sm font-medium leading-6 text-gray-900">Rate</label>
                            <input type="number" step="0.0001" name="rate" id="rate" placeholder="1.00" required
                                class="block w-32 rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <button type="submit"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add</button>
                    </form>
                </div>

                <div class="mt-6 border-t border-gray-100">
                    <dl class="divide-y divide-gray-100">
                        @foreach($currencies as $currency)
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0 items-center">
                                <dt class="text-sm font-medium leading-6 text-gray-900">{{ $currency->code }}
                                    ({{ $currency->symbol }})</dt>
                                <dd
                                    class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0 flex justify-between items-center">
                                    <span>Rate: {{ $currency->rate }}</span>
                                    <div class="flex gap-4">
                                        @if($currency->is_default)
                                            <span
                                                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Default</span>
                                        @else
                                            <form action="{{ route('settings.currency.default', $currency) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">Make
                                                    Default</button>
                                            </form>
                                            <form action="{{ route('settings.currency.destroy', $currency) }}" method="POST"
                                                onsubmit="return confirm('Delete currency?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Users</h3>
                        <p class="mt-2 text-sm text-gray-700">Manage system users and their access.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <a href="{{ route('users.create') }}"
                            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                            User</a>
                    </div>
                </div>
                <div class="mt-8 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                            Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Email</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr>
                                            <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                {{ $user->name }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->email }}
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                @if(auth()->id() !== $user->id)
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                        onsubmit="return confirm('Delete user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 italic">Current User</span>
                                                @endif
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