<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    products: Array,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Low Stock Alert</h2>
        </template>

        <div class="py-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">SKU</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Low Stock Threshold</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.sku }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.stock }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.low_stock_threshold }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
