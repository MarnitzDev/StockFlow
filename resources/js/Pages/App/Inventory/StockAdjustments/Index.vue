<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stockAdjustments: Array,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Adjustments</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <Link :href="route('inventory.stockAdjustments.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Stock Adjustment
                            </Link>
                        </div>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Item
                                </th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    stock
                                </th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Reason
                                </th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="adjustment in stockAdjustments" :key="adjustment.id">
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ adjustment.inventory.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ adjustment.stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ adjustment.type }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ adjustment.reason }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ new Date(adjustment.created_at).toLocaleString() }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
