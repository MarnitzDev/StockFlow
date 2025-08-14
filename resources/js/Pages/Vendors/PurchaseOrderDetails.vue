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
                        <h3 class="text-lg font-semibold mb-4">Order #{{ purchaseOrder.data.order_number }}</h3>
                        <div class="grid grid-cols-3 gap-6 mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 mb-1">Supplier</span>
                                <span class="font-semibold">{{ purchaseOrder.data.vendor }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 mb-1">Date</span>
                                <span class="font-semibold">{{ formatDate(purchaseOrder.data.order_date) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 mb-1">Status</span>
                                <span class="font-semibold">{{ purchaseOrder.data.status }}</span>
                            </div>
                        </div>

                        <h4 class="text-lg font-semibold mt-6 mb-2">Order Items</h4>
                        <DataTable :value="calculatedTotals.items" responsiveLayout="scroll" class="p-datatable-sm">
                            <Column field="product_name" header="Product" />
                            <Column field="quantity" header="Quantity" />
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

                            <ColumnGroup type="footer">
                                <Row>
                                    <Column footer="Total:" :colspan="3" />
                                    <Column>
                                        <template #footer>
                                            <span class="font-black text-lg">
                                                {{ formatCurrency(calculatedTotals.total) }}
                                            </span>
                                        </template>
                                    </Column>
                                </Row>
                            </ColumnGroup>
                        </DataTable>

                        <div v-if="purchaseOrder.status !== 'paid'" class="mt-8">
                            <Button label="Complete Payment" icon="pi pi-check"
                                    @click="completePayment"
                                    :loading="form.processing"
                                    :disabled="form.processing" />
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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Button from 'primevue/button';

const props = defineProps({
    purchaseOrder: Object,
    calculatedTotals: Object,
});

const form = useForm({
    status: 'paid'
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};

const completePayment = () => {
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
