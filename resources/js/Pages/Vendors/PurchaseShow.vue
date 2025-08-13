<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase Order Details</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Order #{{ purchaseOrder.order_number }}</h3>
                        <p><strong>Supplier:</strong> {{ purchaseOrder.vendor.name }}</p>
                        <p><strong>Date:</strong> {{ purchaseOrder.order_date }}</p>
                        <p><strong>Status:</strong> {{ purchaseOrder.status }}</p>

                        <h4 class="text-lg font-semibold mt-6 mb-2">Order Items</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in purchaseOrder.items" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ item.product.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(item.unit_price) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(calculateSubtotal(item)) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(calculateTax(item)) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(calculateTotal(item)) }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Order Summary -->
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold mb-2">Order Summary</h4>
                            <p><strong>Subtotal:</strong> {{ formatCurrency(calculateOrderSubtotal()) }}</p>
                            <p><strong>Tax:</strong> {{ formatCurrency(calculateOrderTax()) }}</p>
                            <p><strong>Total:</strong> {{ formatCurrency(calculateOrderTotal()) }}</p>
                        </div>

                        <!-- Complete Payment button-->
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
import FlashMessage from "@/Components/FlashMessage.vue";
import { computed } from 'vue';

const props = defineProps({
    purchaseOrder: Object,
    pricingConfig: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: 'paid'
});

const taxRate = computed(() => props.pricingConfig.taxRate);
const discountRate = computed(() => props.pricingConfig.discountRate);
const vendorMarkup = computed(() => props.pricingConfig.vendorMarkup);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const calculateSubtotal = (item) => {
    return item.quantity * item.unit_price;
};

const calculateTax = (item) => {
    return calculateSubtotal(item) * taxRate.value;
};

const calculateTotal = (item) => {
    const subtotal = calculateSubtotal(item);
    const tax = calculateTax(item);
    return subtotal + tax;
};

const calculateOrderSubtotal = () => {
    return props.purchaseOrder.items.reduce((sum, item) => sum + calculateSubtotal(item), 0);
};

const calculateOrderTax = () => {
    return props.purchaseOrder.items.reduce((sum, item) => sum + calculateTax(item), 0);
};

const calculateOrderTotal = () => {
    return calculateOrderSubtotal() + calculateOrderTax();
};

const completePayment = () => {
    console.log("complete payment clicked for order:", props.purchaseOrder.id);
    if (form.processing) return;

    form.put(route('vendor.purchases.update', props.purchaseOrder.id), {
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
