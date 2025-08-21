<template>
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="salesOrder">
                        <h3 class="text-lg font-semibold mb-4">Order #{{ salesOrder.order_number }}</h3>

                        <div class="mb-4">
                            <p><strong>Customer:</strong> {{ salesOrder.customer?.name || 'N/A' }}</p>
                            <p><strong>Status:</strong> {{ salesOrder.status || 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> {{ formatCurrency(salesOrder.total_amount) }}</p>
                            <p><strong>Date:</strong> {{ salesOrder.created_at ? new Date(salesOrder.created_at).toLocaleDateString() : 'N/A' }}</p>
                        </div>

                        <h4 class="text-md font-semibold mb-2">Order Items</h4>
                        <DataTable :value="salesOrder.items" class="p-datatable-sm" responsiveLayout="scroll">
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

                        <div class="mt-4">
                            <h4 class="text-md font-semibold mb-2">Notes</h4>
                            <p>{{ salesOrder.notes || 'No notes' }}</p>
                        </div>
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
import { Link } from '@inertiajs/vue3';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import { ref, computed} from "vue";

const { formatCurrency } = useCurrencyFormatter();

const props = defineProps({
    salesOrder: Object,
});

const formatItemName = (item) => {
    return item.inventory?.name || 'N/A';
};
</script>
