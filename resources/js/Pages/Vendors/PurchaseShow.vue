<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase Order Details</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Order #{{ purchaseOrder.order_number }}</h3>
                        <p><strong>Supplier:</strong> {{ purchaseOrder.supplier.name }}</p>
                        <p><strong>Date:</strong> {{ purchaseOrder.order_date }}</p>
                        <p><strong>Status:</strong> {{ purchaseOrder.status }}</p>
                        <p><strong>Total Amount:</strong> ${{ purchaseOrder.total_amount }}</p>

                        <h4 class="text-lg font-semibold mt-6 mb-2">Order Items</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in purchaseOrder.items" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ item.product.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ item.unit_price }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ item.quantity * item.unit_price }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Complete Payment button -->
                        <div v-if="purchaseOrder.status !== 'paid'" class="mt-8">
                            <button
                                @click="completePayment"
                                :disabled="form.processing"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Complete Payment</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';

const props = defineProps({
    purchaseOrder: Object,
});

const form = useForm({
    status: 'paid'
});

const completePayment = () => {
    console.log("complete payment clicked for order:", props.purchaseOrder.id);
    if (form.processing) return;

    form.put(route('supplier.purchases.update', props.purchaseOrder.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Payment completed successfully');
            props.purchaseOrder.status = 'paid';
        },
        onError: (errors) => {
            console.error('Error completing payment:', errors);
        },
    });
};
</script>
