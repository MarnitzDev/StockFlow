<template>
    <Head title="Sale Order" />
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="salesOrder">
                        <h3 class="text-lg font-semibold mb-4">Order #{{ salesOrder.order_number }}</h3>

                        <div class="mb-6 bg-gray-50 rounded-lg p-6 shadow-sm">
                            <h4 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Order Details</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Customer</p>
                                    <p class="font-medium">{{ salesOrder.customer?.name || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <Tag :value="salesOrder.status" :severity="getStatusSeverity(salesOrder.status)" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Amount</p>
                                    <p class="font-medium">{{ formatCurrency(salesOrder.total_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-medium">{{ salesOrder.created_at ? new Date(salesOrder.created_at).toLocaleDateString() : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-md font-semibold mb-2">Order Items</h4>
                        <DataTable :value="salesOrder.items" responsiveLayout="scroll">
                            <Column field="inventory.name" header="Item">
                                <template #body="slotProps">
                                    {{ formatItemName(slotProps.data) }}
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity"></Column>
                            <Column field="unit_price" header="Unit Price">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.unit_price) }}
                                </template>
                            </Column>
                            <Column field="subtotal" header="Subtotal">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.subtotal) }}
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                    <div v-else class="text-red-500">
                        Error: Sales order data not available.
                        <pre>{{ JSON.stringify(salesOrder, null, 2) }}</pre>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('sales.orders.index')" class="mr-4">
                            <Button
                                label="Back to Orders"
                                icon="pi pi-arrow-left"
                                severity="secondary"
                            />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import { ref, computed} from "vue";

const { formatCurrency } = useCurrencyFormatter();

const props = defineProps({
    salesOrder: Object,
});

const formatItemName = (item) => {
    return item.inventory?.name || 'N/A';
};


const getStatusSeverity = (status) => {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'warn';
        case 'processing':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return null;
    }
};
</script>
