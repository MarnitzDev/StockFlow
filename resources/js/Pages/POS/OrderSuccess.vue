<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import POSLayout from '@/Layouts/POSLayout.vue';
import Button from 'primevue/button';
import {ref} from "vue";

interface Order {
    id: number;
    order_number: string;
    total_amount: number;
    items: any[];
    created_at: string;
}

const props = defineProps<{
    order: Order;
}>();

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(amount);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('en-ZA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const breadcrumbItems = ref([
    { icon: 'pi pi-shopping-cart', label: 'POS' },
    { label: 'Purchase Complete' }
]);

</script>

<template>
    <POSLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <Breadcrumb :model="breadcrumbItems" class="p-0">
                    <template #item="{ item }">
                        <span class="flex items-center">
                            <span v-if="item.icon" :class="[item.icon, 'mr-2 text-gray-500']"></span>
                            <span :class="{'font-semibold text-gray-700': !item.icon, 'text-gray-500': item.icon}">
                                {{ item.label }}
                            </span>
                        </span>
                    </template>
                </Breadcrumb>
            </div>
        </template>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <div class="flex flex-col items-center mb-8">
                        <img src="/images/receipt.gif" alt="Order Success" class="w-32 h-32 mb-4" />
                        <h3 class="text-2xl font-bold text-green-600 mb-2">Order Successful!</h3>
                        <p class="text-gray-600">Your order has been successfully placed.</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 mb-8 max-w-xl mx-auto">
                        <div class="flex justify-between mb-4">
                            <span class="font-semibold">Order Number:</span>
                            <span>{{ order.order_number }}</span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="font-semibold">Date:</span>
                            <span>{{ formatDate(order.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold">Total Amount:</span>
                            <span class="text-lg font-bold text-green-600">{{ formatCurrency(order.total_amount) }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between mt-12">
                        <Link :href="route('pos.index')" class="mr-4">
                            <Button
                                label="Back to POS"
                                icon="pi pi-arrow-left"
                                severity="secondary"
                            />
                        </Link>
                        <Button
                            label="Print Receipt"
                            icon="pi pi-print"
                            severity="primary"
                            @click="window.print()"
                        />
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
