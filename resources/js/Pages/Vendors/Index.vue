<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vendors</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <DataView :value="vendors" :layout="layout">
                        <template #header>
                            <div class="flex justify-end">
                                <SelectButton v-model="layout" :allowEmpty="false">
                                    <template #option="{ option }">
                                        <i :class="[option === 'list' ? 'pi pi-bars' : 'pi pi-table']" />
                                    </template>
                                </SelectButton>
                            </div>
                        </template>

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
                                            <div class="flex flex-row md:flex-col justify-between items-start gap-2">
                                                <div>
                                                    <div class="text-lg font-medium mt-2">{{ vendor.name }}</div>
                                                    <div class="text-sm text-gray-600">{{ vendor.email }}</div>
                                                    <div class="text-sm text-gray-600">{{ vendor.phone }}</div>
                                                    <div class="text-sm text-gray-600">{{ vendor.address }}</div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col md:items-end gap-8">
                                                <div class="flex flex-row-reverse md:flex-row gap-2">
                                                    <Link
                                                        :href="route('vendor.show', vendor.id)"
                                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 transition ease-in-out duration-150"
                                                    >
                                                        <i class="pi pi-shopping-cart mr-2"></i>
                                                        View Catalogue
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
import { Head } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Link } from '@inertiajs/vue3';
import DataView from 'primevue/dataview';
import SelectButton from 'primevue/selectbutton';

interface Vendor {
    id: number;
    name: string;
    product_count: number;
}

const props = defineProps<{
    vendors: Vendor[];
}>();

const layout = ref('list');

const getVendorInitials = (name: string): string => {
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .slice(0, 3);
};
</script>
