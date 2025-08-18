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
                <Button
                    icon="pi pi-question-circle"
                    iconPos="right"
                    label="Guide"
                    @click="showHelp"
                    class="p-button-text p-button-rounded"
                    aria-label="Help Guide"
                />
            </div>
        </template>

        <Dialog v-model:visible="visible" modal header="How to Use the StockFlow Vendors"  :breakpoints="{ '960px': '75vw', '640px': '90vw' }">
            <VendorHelpContent />
        </Dialog>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <DataView :value="vendors" :layout="layout">
                        <template #list="slotProps">
                            <div class="flex flex-col">
                                <div v-for="(vendor, index) in slotProps.items" :key="vendor.id">
                                    <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4" :class="{ 'border-t border-surface-200 dark:border-surface-700': index !== 0 }">
                                        <div class="md:w-40 relative">
                                            <div class="bg-gray-200 h-40 w-40 flex items-center justify-center rounded">
                                                <span class="text-4xl font-bold text-gray-500">{{ getVendorInitials(vendor.name) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                            <div class="flex flex-col justify-between items-start gap-2">
                                                <div class="text-lg font-semibold text-gray-800">{{ vendor.name }}</div>
                                                <div class="flex flex-col text-sm text-gray-600 space-y-2">
                                                    <div class="flex items-center gap-3">
                                                        <i class="pi pi-envelope text-gray-400"></i>
                                                        <span class="truncate">{{ vendor.email }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <i class="pi pi-phone text-gray-400"></i>
                                                        <span>{{ vendor.phone }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <i class="pi pi-map-marker text-gray-400"></i>
                                                        <span class="truncate">{{ vendor.address }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col md:items-end gap-8">
                                                <div class="flex flex-row-reverse md:flex-row gap-2">
                                                    <Link
                                                        :href="route('vendors.show', vendor.id)" class="no-underline">
                                                        <Button
                                                            label="View Catalogue"
                                                            icon="pi pi-book"
                                                            severity="primary"
                                                        />
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </DataView>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useDialog } from 'primevue/usedialog';

const dialog = useDialog();
const visible = ref(false);

interface Vendor {
    id: number;
    name: string;
    product_count: number;
}

const props = defineProps<{
    vendors: Vendor[];
}>();

const layout = ref('list');

const breadcrumbItems = ref([
    { icon: 'pi pi-truck', label: 'Vendors' },
    { label: 'View All' }
]);

const showHelp = () => {
    visible.value = true;
};

const VendorHelpContent = {
    template: `
        <div class="p-6 bg-gray-50 rounded-lg">
            <ol class="list-decimal pl-5 space-y-4">
                <li class="text-gray-700">Click <span class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-semibold">View Catalogue</span> on a vendor card to see their products.</li>
                <li class="text-gray-700">Purchase Order Process:
                    <ul class="list-none mt-2 space-y-4">
                        <li class="flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 bg-white text-blue-500 border border-blue-500 rounded-full mr-3">1</span>
                            <span><span class="font-semibold">Create:</span> Select products and click <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">Review Order</span></span>
                        </li>
                        <li class="flex items-center">
                           <span class="flex items-center justify-center w-8 h-8 bg-white text-blue-500 border border-blue-500 rounded-full mr-3">2</span>
                            <span><span class="font-semibold">Review:</span> Check details, then <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">Proceed to Payment</span></span>
                        </li>
                        <li class="flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 bg-white text-blue-500 border border-blue-500 rounded-full mr-3">3</span>
                            <span><span class="font-semibold">Pay:</span> Choose payment method and <span class="inline-flex items-center bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold"><i class="pi pi-shopping-cart mr-1"></i>Complete Order</span</span>
                        </li>
                    </ul>
                </li>
            </ol>
            <div class="mt-6 bg-blue-50 p-4 rounded-lg">
               <ul class="space-y-2 text-blue-800">
                 <li><span class="font-semibold">Inventory:</span> Browse a vendor’s catalogue to add items and restock inventory.</li>
                  <li><span class="font-semibold">POS Sync:</span> Purchased items appear in your POS for sale.</li>
                  <li><span class="font-semibold">Low Stock Alerts:</span> Use alerts to reorder quickly from vendors.</li>
                  <li><span class="font-semibold">Product Variety:</span> Vendors may offer unique items, categories, or brands.</li>
                </ul>
            </div>
        </div>
    `
};

const getVendorInitials = (name: string): string => {
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .slice(0, 3);
};
</script>
