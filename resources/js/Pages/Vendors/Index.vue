<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vendors</h2>
        </template>
        <div class="py-12">
            <div class="px-6">
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
                                                            severity="contrast"
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
