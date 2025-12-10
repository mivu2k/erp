@extends('layouts.app')

@section('content')
    <div>
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Edit
                    Supplier</h2>
            </div>
        </div>

        <form action="{{ route('suppliers.update', $supplier) }}" method="POST"
            class="mt-8 space-y-8 divide-y divide-gray-200">
            @csrf
            @method('PUT')
            <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
                <div class="space-y-6 sm:space-y-5">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Supplier Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Update details about the supplier.</p>
                    </div>

                    <div class="space-y-6 sm:space-y-5">
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="name"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Name</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="name" id="name" value="{{ $supplier->name }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="contact_person"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Contact Person</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="contact_person" id="contact_person"
                                    value="{{ $supplier->contact_person }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="email"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Email</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="email" name="email" id="email" value="{{ $supplier->email }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="phone"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Phone</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="phone" id="phone" value="{{ $supplier->phone }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="country"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Country</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="country" id="country" value="{{ $supplier->country }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end gap-x-3">
                    <a href="{{ route('suppliers.index') }}"
                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Update
                        Supplier</button>
                </div>
            </div>
        </form>
    </div>
@endsection