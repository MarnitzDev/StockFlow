<template>
    <VendorLayout>
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

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <div class="flex flex-col items-center mb-8">
                        <img src="/images/receipt.gif" alt="Receipt" class="w-32 h-32 mb-4" />
                        <h3 class="text-2xl font-bold text-green-600 mb-2">{{ message }}</h3>
                        <p class="text-gray-600">Your purchase order has been successfully created.</p>
                    </div>

                    <div class="bg-gray-100 rounded-lg p-6 mb-8">
                        <div class="flex justify-between mb-4">
                            <span class="font-semibold">Purchase Order Number:</span>
                            <span>{{ purchaseOrder.order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold">Total Amount:</span>
                            <span class="text-lg font-bold text-green-600">{{ formatCurrency(purchaseOrder.total_amount) }}</span>
                        </div>
                    </div>

                    <div class="flex justify-center mt-12">
                        <Link :href="route('vendors.index')" class="mr-4">
                            <Button
                                label="Back to Vendors"
                                icon="pi pi-arrow-left"
                                variant="text"
                                class="p-button-primary"
                            />
                        </Link>
                        <Link :href="route('vendors.purchases.index')" class="mr-4">
                            <Button
                                label="View Purchase History"
                                icon="pi pi-history"
                                class="p-button-secondary"
                            />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import {ref} from "vue";

const props = defineProps({
    message: String,
    purchaseOrder: Object,
    vendorId: Number,
});

const breadcrumbItems = ref([
    { icon: 'pi pi-truck', label: 'Vendor' },
    { label: 'Purchase Order Success' }
]);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(amount);
};
</script>
