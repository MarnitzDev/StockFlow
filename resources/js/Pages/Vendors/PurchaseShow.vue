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
                        <h3 class="text-lg font-semibold mb-4">Order #{{ purchaseOrder.order_number }}</h3>
                        <div class="grid grid-cols-3 gap-6 mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 mb-1">Supplier</span>
                                <span class="font-semibold">{{ purchaseOrder.vendor.name }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 mb-1">Date</span>
                                <span class="font-semibold">{{ formatDate(purchaseOrder.order_date) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 mb-1">Status</span>
                                <span class="font-semibold">{{ purchaseOrder.status }}</span>
                            </div>
                        </div>


                        <h4 class="text-lg font-semibold mt-6 mb-2">Order Items</h4>
                        <DataTable :value="purchaseOrder.items" responsiveLayout="scroll" class="p-datatable-sm">
                            <ColumnGroup type="header">
                                <Row>
                                    <Column header="Product Details" :colspan="2" />
                                    <Column header="Pricing" :colspan="4" />
                                </Row>
                                <Row>
                                    <Column header="Product" />
                                    <Column header="Quantity" />
                                    <Column header="Unit Price" />
                                    <Column header="Subtotal" />
                                    <Column header="Tax" />
                                    <Column header="Total" />
                                </Row>
                            </ColumnGroup>

                            <Column field="product.name" />
                            <Column field="quantity" />
                            <Column field="unit_price">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.unit_price) }}
                                </template>
                            </Column>
                            <Column>
                                <template #body="slotProps">
                                    {{ formatCurrency(calculateSubtotal(slotProps.data)) }}
                                </template>
                            </Column>
                            <Column>
                                <template #body="slotProps">
                                    {{ formatCurrency(calculateTax(slotProps.data)) }}
                                </template>
                            </Column>
                            <Column>
                                <template #body="slotProps">
                                    {{ formatCurrency(calculateTotal(slotProps.data)) }}
                                </template>
                            </Column>

                            <ColumnGroup type="footer">
                                <Row>
                                    <Column footer="Totals:" :colspan="3" />
                                    <Column>
                                        <template #footer>
                                            <span>
                                                {{ formatCurrency(calculateOrderSubtotal()) }}
                                            </span>
                                        </template>
                                    </Column>
                                    <Column>
                                        <template #footer>
                                            <span>
                                                {{ formatCurrency(calculateOrderTax()) }}
                                            </span>
                                        </template>
                                    </Column>
                                    <Column>
                                        <template #footer>
                                            <span class="font-black text-lg">
                                                {{ formatCurrency(calculateOrderTotal()) }}
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
import { computed } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
    purchaseOrder: Object,
    pricingConfig: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: 'paid'
});

const taxRate = computed(() => props.pricingConfig.taxRate);
const discountRate = computed(() => props.pricingConfig.discountRate);
const vendorMarkup = computed(() => props.pricingConfig.vendorMarkup);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};

const calculateSubtotal = (item) => {
    return item.quantity * item.unit_price;
};

const calculateTax = (item) => {
    return calculateSubtotal(item) * taxRate.value;
};

const calculateTotal = (item) => {
    const subtotal = calculateSubtotal(item);
    const tax = calculateTax(item);
    return subtotal + tax;
};

const calculateOrderSubtotal = () => {
    return props.purchaseOrder.items.reduce((sum, item) => sum + calculateSubtotal(item), 0);
};

const calculateOrderTax = () => {
    return props.purchaseOrder.items.reduce((sum, item) => sum + calculateTax(item), 0);
};

const calculateOrderTotal = () => {
    return calculateOrderSubtotal() + calculateOrderTax();
};

const orderSummary = computed(() => [
    { label: 'Subtotal', value: calculateOrderSubtotal() },
    { label: 'Tax', value: calculateOrderTax() },
    { label: 'Total', value: calculateOrderTotal() }
]);

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
