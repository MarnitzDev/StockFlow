<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    currentStock: Array,
    outOfStock: Array,
    inventoryValuation: Number,
});
</script>

<template>
    <Head title="Inventory Report" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Report</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Current Stock Levels</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in currentStock" :key="item.sku">
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.category }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-semibold mt-8 mb-4">Out-of-Stock Items</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in outOfStock" :key="item.sku">
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.category }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-semibold mt-8 mb-4">Inventory Valuation</h3>
                    <p class="text-2xl font-bold">${{ inventoryValuation.toFixed(2) }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
