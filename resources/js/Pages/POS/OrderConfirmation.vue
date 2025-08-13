<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import POSLayout from '@/Layouts/POSLayout.vue';

interface OrderItem {
    id: number;
    name: string;
    quantity: number;
    price: number;
}

interface Order {
    id: number;
    order_number: string;
    total: number;
    items: OrderItem[];
    created_at: string;
}

const props = defineProps<{
    order: Order;
}>();

const printReceipt = () => {
    window.print();
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
    <Head title="Order Confirmation" />

    <POSLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order Confirmation</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-4">Order #{{ order.order_number }}</h1>
                    <p class="mb-4">Date: {{ new Date(order.created_at).toLocaleString() }}</p>

                    <table class="w-full mb-6">
                        <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Item</th>
                            <th class="text-right py-2">Quantity</th>
                            <th class="text-right py-2">Price</th>
                            <th class="text-right py-2">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in order.items" :key="item.id" class="border-b">
                            <td class="py-2">{{ item.inventory.name }}</td>
                            <td class="text-right py-2">{{ item.quantity }}</td>
                            <td class="text-right py-2">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="text-right py-2">{{ formatCurrency(item.quantity * item.unit_price) }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold py-2">Total:</td>
                            <td class="text-right font-bold py-2">{{ formatCurrency(order.total_amount) }}</td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="flex justify-between mt-8">
                        <Link :href="route('pos.index')">
                            <Button label="Return to POS" icon="pi pi-arrow-left" class="p-button-secondary" />
                        </Link>
                        <Button label="Print Receipt" icon="pi pi-print" @click="printReceipt" />
                    </div>
                </div>
            </div>
        </div>
    </POSLayout>
</template>

<style scoped>
@media print {
    .py-12 {
        padding: 0;
    }
    .shadow-sm {
        box-shadow: none;
    }
    button {
        display: none;
    }
}
</style>
