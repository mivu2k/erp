@extends('layouts.app')

@section('content')
    <div>
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Edit
                    Item</h2>
            </div>
        </div>

        <form action="{{ route('materials.update', $stone) }}" method="POST"
            class="mt-8 space-y-8 divide-y divide-gray-200">
            @csrf
            @method('PUT')
            <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
                <div class="space-y-6 sm:space-y-5">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Material Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Update details about the stone type or material.</p>
                    </div>

                    <div class="space-y-6 sm:space-y-5">
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="type"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Type</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <select id="type" name="type" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    @foreach(['Marble', 'Granite', 'Quartzite', 'Onyx', 'Travertine', 'Limestone', 'Other'] as $type)
                                        <option value="{{ $type }}" {{ $stone->type == $type ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Category
                                Name</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="name" id="name" value="{{ $stone->name }}" required
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="color"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Color</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="color" id="color" value="{{ $stone->color }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="size" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Size /
                                Thickness</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="size" id="size" value="{{ $stone->size }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="color_finish"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Finish</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input type="text" name="color_finish" id="color_finish" value="{{ $stone->color_finish }}"
                                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:border-t sm:border-gray-200 sm:pt-5">
                            <label for="description"
                                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Description</label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <textarea id="description" name="description" rows="3"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xl sm:text-sm sm:leading-6">{{ $stone->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end gap-x-3">
                    <a href="{{ route('materials.index') }}"
                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Update
                        Material</button>
                </div>
            </div>
        </form>
    </div>
@endsection