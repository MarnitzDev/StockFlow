<template>
    <Head :title="'View Item - ' + item.name" />

    <AuthenticatedLayout>
<!--        <template #header>-->
<!--            <h2 class="font-semibold text-xl text-gray-800 leading-tight">-->
<!--                Item Details-->
<!--            </h2>-->
<!--        </template>-->

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full md:w-1/5 px-4 mb-8 md:mb-0">
                                <div class="flex items-center justify-center rounded-lg bg-gray-200 w-200 h-200">
                                    <img
                                        :src="item.image_url || '/images/placeholder-item.svg'"
                                        :alt="item.name"
                                        class="w-200 h-200 rounded-lg object-cover"
                                        @error="handleImageError"
                                    >
                                </div>
                            </div>
                            <div class="w-full md:w-4/5 px-4">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ item.name }}</h1>
                                <p class="text-2xl font-semibold text-gray-700 mb-4">{{ formatCurrency(item.price) }}</p>

                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                                    <p class="text-gray-600">{{ item.description || 'No description available.' }}</p>
                                </div>

                                <div class="border-t border-b border-gray-200 py-4 mb-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-500">SKU</h3>
                                            <p class="text-gray-900">{{ item.sku }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-500">Category</h3>
                                            <p class="text-gray-900">{{ item.category.name }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-500">Supplier</h3>
                                            <p class="text-gray-900">{{ item.supplier ? item.supplier.name : 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-500">Stock</h3>
                                            <p class="text-gray-900">{{ item.stock }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-500">Low Stock Threshold</h3>
                                            <p class="text-gray-900">{{ item.low_stock_threshold }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-500">Available on POS</h3>
                                            <p class="text-gray-900">{{ item.available_on_pos ? 'Yes' : 'No' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Back and Edit buttons -->
                                <div class="mt-8 flex justify-between">
                                    <Link :href="route('inventory.items')" class="mr-4">
                                        <Button
                                            label="Back to Inventory"
                                            icon="pi pi-arrow-left"
                                            severity="secondary"
                                        />
                                    </Link>
                                    <Link :href="route('inventory.edit', item.id)">
                                        <Button
                                            label="Edit Item"
                                            icon="pi pi-pencil"
                                            severity="primary"
                                        />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head } from '@inertiajs/vue3';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const { formatCurrency } = useCurrencyFormatter();

defineProps({
    item: Object
});

const handleImageError = (event) => {
    event.target.src = '/images/placeholder-item.svg';
};
</script>
