@extends('layouts.app')

@section('content')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Edit Fabrication Job #{{ $workOrder->id }}
            </h2>
        </div>
    </div>

    <div class="mt-8" x-data="fabricationForm()">
        <form action="{{ route('work_orders.update', $workOrder) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Source Item Selection -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">1. Select Source Item</h3>
                            <div class="mt-2 max-w-xl text-sm text-gray-500">
                                <p>Choose the item (slab/counter) you want to cut or transform.</p>
                            </div>

                            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <!-- Step 1: Material -->
                                <div>
                                    <label for="material_select"
                                        class="block text-sm font-medium leading-6 text-gray-900">Material</label>
                                    <select id="material_select" x-model="selectedMaterialId" @change="onMaterialChange()"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">Select Material...</option>
                                        <template x-for="mat in materialsWithStock" :key="mat.id">
                                            <option :value="mat.id"
                                                x-text="mat.name + (mat.color ? ' - ' + mat.color : '')"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Step 2: Lot -->
                                <div>
                                    <label for="lot_select"
                                        class="block text-sm font-medium leading-6 text-gray-900">Lot</label>
                                    <select id="lot_select" x-model="selectedBatchId" @change="onBatchChange()"
                                        :disabled="!selectedMaterialId"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100 disabled:text-gray-400">
                                        <option value="">Select Lot...</option>
                                        <template x-for="batch in availableBatches" :key="batch.id">
                                            <option :value="batch.id" x-text="batch.block_number"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Step 3: Item -->
                                <div>
                                    <label for="item_select" class="block text-sm font-medium leading-6 text-gray-900">Item
                                        (Slab)</label>
                                    <select id="item_select" name="source_item_id" x-model="sourceItemId"
                                        @change="updateSourceDetails()" :disabled="!selectedBatchId"
                                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100 disabled:text-gray-400">
                                        <option value="">Select Item...</option>
                                        <template x-for="item in availableItems" :key="item.id">
                                            <option :value="item.id"
                                                x-text="'#' + item.slab_number + ' (' + item.width_in + 'x' + item.length_in + ')'">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Selected Item Details -->
                            <div class="mt-4 rounded-md bg-gray-50 p-4" x-show="sourceItemId" x-transition>
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-cube text-indigo-600 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-800">Selected: <span
                                                x-text="sourceName"></span>
                                        </h3>
                                        <div class="mt-2 text-sm text-gray-700 grid grid-cols-2 gap-4">
                                            <p>Dimensions: <span class="font-semibold" x-text="sourceDims"></span></p>
                                            <p>Area: <span class="font-semibold" x-text="sourceSqft"></span> sqft</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Output Items Definition -->
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">2. Define Outputs</h3>
                            <div class="mt-2 max-w-xl text-sm text-gray-500">
                                <p>Specify what you are making from the source item.</p>
                            </div>

                            <div class="mt-5 space-y-4">
                                <template x-for="(output, index) in outputs" :key="index">
                                    <div
                                        class="relative flex flex-col sm:flex-row sm:items-end gap-4 rounded-md border border-gray-200 p-4 bg-gray-50 hover:border-indigo-300 transition-colors">
                                        <div class="flex-1 min-w-[200px]">
                                            <label class="block text-sm font-medium text-gray-700">Product /
                                                Material</label>
                                            <select required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                :name="'outputs[' + index + '][material_id]'" x-model="output.material_id">
                                                <option value="">Select Material...</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->name }} ({{ $material->color }} - {{ $material->size }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700">Width (in)</label>
                                            <input type="number" step="0.01" :name="'outputs[' + index + '][width_in]'"
                                                x-model="output.width_in" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700">Length (in)</label>
                                            <input type="number" step="0.01" :name="'outputs[' + index + '][length_in]'"
                                                x-model="output.length_in" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="w-24">
                                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                            <input type="number" min="1" :name="'outputs[' + index + '][quantity]'"
                                                x-model="output.quantity" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="flex-1 pt-6 text-right">
                                            <p class="text-sm text-gray-500">
                                                <span class="font-medium text-gray-900"
                                                    x-text="((output.width_in * output.length_in * output.quantity) / 144).toFixed(2)"></span>
                                                sqft
                                            </p>
                                        </div>
                                        <button type="button" @click="removeOutput(index)"
                                            class="text-red-400 hover:text-red-600 p-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-4">
                                <button type="button" @click="addOutput()"
                                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <i class="fas fa-plus mr-2"></i> Add Output Item
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">3. Job Details</h3>
                            <div class="mt-5">
                                <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Notes /
                                    Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $workOrder->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow sm:rounded-lg sticky top-8">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Yield Summary</h3>
                            <dl class="mt-5 grid grid-cols-1 gap-5">
                                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 sm:p-6">
                                    <dt class="truncate text-sm font-medium text-gray-500">Total Input Area</dt>
                                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                                        <span x-text="sourceSqft || '0.00'"></span> <span
                                            class="text-sm font-normal text-gray-500">sqft</span>
                                    </dd>
                                </div>
                                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 sm:p-6">
                                    <dt class="truncate text-sm font-medium text-gray-500">Total Output Area</dt>
                                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-indigo-600">
                                        <span x-text="totalOutputSqft"></span> <span
                                            class="text-sm font-normal text-gray-500">sqft</span>
                                    </dd>
                                </div>
                                <div class="overflow-hidden rounded-lg px-4 py-5 sm:p-6"
                                    :class="wasteSqft >= 0 ? 'bg-green-50' : 'bg-red-50'">
                                    <dt class="truncate text-sm font-medium"
                                        :class="wasteSqft >= 0 ? 'text-green-800' : 'text-red-800'">
                                        Waste / Loss
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold tracking-tight"
                                        :class="wasteSqft >= 0 ? 'text-green-900' : 'text-red-900'">
                                        <span x-text="wasteSqft"></span> <span class="text-sm font-normal">sqft</span>
                                    </dd>
                                    <p class="mt-1 text-sm" :class="wasteSqft >= 0 ? 'text-green-700' : 'text-red-700'">
                                        <span x-text="wastePercentage"></span>%
                                    </p>
                                </div>
                            </dl>
                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <button type="submit"
                                    class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Update Fabrication
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function fabricationForm() {
            return {
                materialsWithStock: @json($materialsWithStock),
                selectedMaterialId: '',
                selectedBatchId: '',
                sourceItemId: '{{ $sourceItem ? $sourceItem->id : "" }}',

                sourceName: '',
                sourceDims: '',
                sourceSqft: '',
                outputs: [
                    @foreach($outputs as $output)
                            {
                        width_in: {{ $output->width_in }},
                        length_in: {{ $output->length_in }},
                        material_id: {{ $output->stone_id }},
                        quantity: {{ $output->quantity_pieces }}
                            },
                    @endforeach
                    ],

                get availableBatches() {
                    if (!this.selectedMaterialId) return [];
                    const mat = this.materialsWithStock.find(m => m.id == this.selectedMaterialId);
                    return mat ? mat.batches : [];
                },

                get availableItems() {
                    if (!this.selectedBatchId) return [];
                    const batches = this.availableBatches;
                    const batch = batches.find(b => b.id == this.selectedBatchId);
                    return batch ? batch.items : [];
                },

                init() {
                    if (this.sourceItemId) {
                        this.findAndSetSourceHierarchy();
                    }
                    if (this.outputs.length === 0) {
                        this.addOutput();
                    }
                },

                findAndSetSourceHierarchy() {
                    // Iterate through materials -> batches -> items to find sourceItemId
                    for (const mat of this.materialsWithStock) {
                        for (const batch of mat.batches) {
                            const item = batch.items.find(i => i.id == this.sourceItemId);
                            if (item) {
                                this.selectedMaterialId = mat.id;
                                this.selectedBatchId = batch.id;
                                this.updateSourceDetails(item);
                                return;
                            }
                        }
                    }
                },

                onMaterialChange() {
                    this.selectedBatchId = '';
                    this.sourceItemId = '';
                    this.updateSourceDetails();
                },

                onBatchChange() {
                    this.sourceItemId = '';
                    this.updateSourceDetails();
                },

                updateSourceDetails(itemObj = null) {
                    let item = itemObj;
                    if (!item && this.sourceItemId) {
                        // Find item in availableItems
                        item = this.availableItems.find(i => i.id == this.sourceItemId);
                    }

                    if (item) {
                        this.sourceName = (this.materialsWithStock.find(m => m.id == this.selectedMaterialId)?.name || '') + ' #' + item.slab_number;
                        this.sourceDims = item.width_in + '" x ' + item.length_in + '"';
                        this.sourceSqft = parseFloat(item.sqft).toFixed(2);
                    } else {
                        this.sourceName = '';
                        this.sourceDims = '';
                        this.sourceSqft = '';
                    }
                },

                addOutput() {
                    this.outputs.push({ width_in: '', length_in: '', material_id: '', quantity: 1 });
                },

                removeOutput(index) {
                    this.outputs.splice(index, 1);
                },

                get totalOutputSqft() {
                    let total = 0;
                    this.outputs.forEach(o => {
                        if (o.width_in && o.length_in && o.quantity) {
                            total += (o.width_in * o.length_in * o.quantity) / 144;
                        }
                    });
                    return total.toFixed(2);
                },

                get wasteSqft() {
                    if (!this.sourceSqft) return 0;
                    return (this.sourceSqft - this.totalOutputSqft).toFixed(2);
                },

                get wastePercentage() {
                    if (!this.sourceSqft || this.sourceSqft == 0) return 0;
                    return ((this.wasteSqft / this.sourceSqft) * 100).toFixed(1);
                }
            }
        }
    </script>
@endsection