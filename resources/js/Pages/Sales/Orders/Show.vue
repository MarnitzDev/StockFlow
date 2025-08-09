<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    salesOrder: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order Details</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="salesOrder">
                        <h3 class="text-lg font-semibold mb-4">Order #{{ salesOrder.order_number }}</h3>

                        <div class="mb-4">
                            <p><strong>Customer:</strong> {{ salesOrder.customer?.name || 'N/A' }}</p>
                            <p><strong>Status:</strong> {{ salesOrder.status || 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> ${{ salesOrder.total_amount || '0.00' }}</p>
                            <p><strong>Date:</strong> {{ salesOrder.created_at ? new Date(salesOrder.created_at).toLocaleDateString() : 'N/A' }}</p>
                        </div>

                        <h4 class="text-md font-semibold mb-2">Order Items</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in salesOrder.items" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ item.inventory?.name || 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity || '0' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ item.unit_price || '0.00' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ item.subtotal || '0.00' }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="mt-4">
                            <h4 class="text-md font-semibold mb-2">Notes</h4>
                            <p>{{ salesOrder.notes || 'No notes' }}</p>
                        </div>
                    </div>
                    <div v-else class="text-red-500">
                        Error: Sales order data not available.
                        <pre>{{ JSON.stringify(salesOrder, null, 2) }}</pre>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('sales.orders.index')" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                            Back to Orders
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
