<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    salesOrders: Array,
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const formatDate = (value) => {
    if (value) {
        return new Date(value).toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }
    return '';
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable
                            :value="salesOrders"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            filterDisplay="menu"
                            :globalFilterFields="['order_number', 'customer.name', 'total_amount', 'status']"
                            responsiveLayout="scroll"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0">Sales Orders</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                                    </IconField>
                                </div>
                            </template>

                            <Column field="order_number" header="Order Number" sortable></Column>
                            <Column field="created_at" header="Date" sortable>
                                <template #body="slotProps">
                                    {{ formatDate(slotProps.data.created_at) }}
                                </template>
                            </Column>
                            <Column field="customer.name" header="Customer" sortable></Column>
                            <Column field="total_amount" header="Total Amount" sortable>
                                <template #body="slotProps">
                                    R {{ slotProps.data.total_amount }}
                                </template>
                            </Column>
                            <Column field="status" header="Status" sortable></Column>
                            <Column header="Actions">
                                <template #body="slotProps">
                                    <Link
                                        :href="route('sales.orders.show', slotProps.data.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-2"
                                        title="View Order Details"
                                    >
                                        <i class="pi pi-eye mr-1"></i> View
                                    </Link>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
