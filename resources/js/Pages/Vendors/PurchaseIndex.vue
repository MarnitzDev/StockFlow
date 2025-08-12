<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase Orders</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in purchaseOrders" :key="order.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ order.order_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ order.supplier.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ order.order_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ order.total_amount }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ order.status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('supplier.purchases.show', order.id)" class="text-indigo-600 hover:text-indigo-900">View</Link>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    purchaseOrders: Array,
});
</script>
