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
                <span class="text-lg font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">
                    Vendor: {{ vendor.name }}
                </span>
            </div>
        </template>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="card">
                    <Stepper v-model:activeStep="activeStep" value="1" class="">
                        <StepList>
                            <Step value="1">Create Order</Step>
                            <Step value="2">Review Details</Step>
                            <Step value="3">Payment</Step>
                        </StepList>
                        <StepItem value="1">
                            <StepPanel v-slot="{ activateCallback }" class="h-full flex flex-col p-12">
                                <div class="flex-grow overflow-auto">
                                    <div class="bg-surface-50 dark:bg-surface-950">
                                        <DataTable
                                            :value="calculatedTotals.items"
                                            :paginator="true"
                                            :rows="10"
                                            :filters="filters"
                                            filterDisplay="menu"
                                            :globalFilterFields="['name', 'sku', 'unit_price', 'quantity']"
                                            responsiveLayout="scroll"
                                            size="small"
                                        >
                                            <template #header>
                                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                                    <h4 class="text-xl font-bold">Purchase Order Items</h4>
                                                    <IconField>
                                                        <InputIcon>
                                                            <i class="pi pi-search" />
                                                        </InputIcon>
                                                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                                                    </IconField>
                                                </div>
                                            </template>

                                            <ColumnGroup type="header">
                                                <Row>
                                                    <Column header="Product" rowspan="2" style="width: 45%;" />
                                                    <Column header="Stock" rowspan="2" style="width: 10%;" />
                                                    <Column header="Unit Price" rowspan="2" style="width: 15%;" />
                                                    <Column header="Total" rowspan="2" style="width: 15%;" />
                                                    <Column header="Quantity" colspan="2" style="width: 15%;" />
                                                </Row>
                                            </ColumnGroup>

                                            <Column field="name" header="Product">
                                                <template #body="slotProps">
                                                    <div class="flex items-center">
                                                        <span :class="[
                                                            'text-sm font-medium',
                                                            slotProps.data.stock > 0 && quantities[slotProps.data.id] < slotProps.data.stock
                                                                ? 'text-gray-900'
                                                                : 'text-gray-400'
                                                        ]">
                                                            {{ slotProps.data.name }}
                                                        </span>
                                                        <span v-if="slotProps.data.stock === 0" class="ml-2 text-xs">
                                                            (Out of Stock)
                                                        </span>
                                                        <span v-else-if="quantities[slotProps.data.id] >= slotProps.data.stock" class="ml-2 text-xs">
                                                            (Max Quantity Added)
                                                        </span>
                                                    </div>
                                                </template>
                                            </Column>
                                            <Column field="stock" header="Stock">
                                                <template #body="slotProps">
                                                    {{ slotProps.data.stock }}
                                                </template>
                                            </Column>
                                            <Column field="unit_price" header="Unit Price">
                                                <template #body="slotProps">
                                                    {{ formatCurrency(slotProps.data.unit_price) }}
                                                </template>
                                            </Column>
                                            <Column header="Total">
                                                <template #body="slotProps">
                                                    {{ (quantities[slotProps.data.id] || 0) > 0
                                                    ? formatCurrency(slotProps.data.unit_price * quantities[slotProps.data.id])
                                                    : '' }}
                                                </template>
                                            </Column>
                                            <Column header="Buy">
                                                <template #body="slotProps">
                                                    <div class="flex items-center gap-2">
                                                        <InputNumber
                                                            v-model="quantities[slotProps.data.id]"
                                                            :min="0"
                                                            :max="slotProps.data.stock"
                                                            :disabled="slotProps.data.stock === 0"
                                                            showButtons
                                                            class="w-20"
                                                            size="small"
                                                            :inputStyle="{ width: '2rem' }"
                                                        />
                                                    </div>
                                                </template>
                                            </Column>
                                        </DataTable>
                                    </div>
                                </div>
                                <div class="pt-6 mt-auto border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div class="text-lg font-semibold">
                                            Total: {{ formatCurrency(cartTotal) }}
                                            <div class="text-sm text-gray-600">
                                                {{ calculateTotalItems() }} item{{ calculateTotalItems() !== 1 ? 's' : '' }} selected
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <Button
                                                @click="(event) => createPurchaseOrder(event, activateCallback)"
                                                label="Review Order"
                                                icon="pi pi-arrow-right"
                                                iconPos="right"
                                                severity="success"
                                                :disabled="!cartTotal"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </StepPanel>
                        </StepItem>

                        <StepItem value="2">
                            <StepPanel v-slot="{ activateCallback }" class="h-full flex flex-col p-12">
                                <div v-if="purchaseOrder" class="flex-grow overflow-auto">
                                    <DataTable :value="purchaseOrder.items" class="p-datatable-sm">
                                        <template #header>
                                            <h4 class="text-xl font-bold mb-2">Order Items</h4>
                                        </template>
                                        <Column field="name" header="Product"></Column>
                                        <Column field="quantity" header="Quantity"></Column>
                                        <Column field="unit_price" header="Unit Price">
                                            <template #body="slotProps">
                                                {{ formatCurrency(slotProps.data.unit_price) }}
                                            </template>
                                        </Column>
                                        <Column header="Total">
                                            <template #body="slotProps">
                                                {{ formatCurrency(slotProps.data.unit_price * slotProps.data.quantity) }}
                                            </template>
                                        </Column>
                                        <ColumnGroup type="footer">
                                            <Row>
                                                <Column footer="Total:" colspan="3"></Column>
                                                <Column :footer="formatCurrency(purchaseOrder.total)"></Column>
                                            </Row>
                                        </ColumnGroup>
                                    </DataTable>
                                    <div class="bg-white overflow-hidden py-12">
                                        <div class="grid grid-cols-3 gap-6 ml-2">
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-600 mb-1">Order Number</span>
                                                <span class="font-semibold">{{ purchaseOrder.order_number }}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-600 mb-1">Supplier</span>
                                                <span class="font-semibold">{{ purchaseOrder.vendor.name }}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-600 mb-1">Date</span>
                                                <span class="font-semibold">{{ formatDate(purchaseOrder.order_date) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="pt-6 mt-auto border-t border-gray-200">
                                    <div class="flex justify-between gap-4">
                                        <Button label="Back"
                                                severity="secondary"
                                                icon="pi pi-arrow-left"
                                                @click="activateCallback('1')"
                                        />
                                        <Button
                                            @click="activateCallback('3')"
                                            label="Proceed to Payment"
                                            icon="pi pi-arrow-right"
                                            iconPos="right"
                                            severity="success"
                                        />
                                    </div>
                                </div>
                            </StepPanel>
                        </StepItem>

                        <StepItem value="3">
                            <StepPanel v-slot="{ activateCallback }" class="h-full flex flex-col p-12">
                                <div class="bg-white overflow-hidden sm:rounded-lg">
                                    <h4 class="text-xl font-bold mb-8">Finalise Payment</h4>
                                    <div class="grid grid-cols-2 gap-6 mb-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-600 mb-1">Payment Method</span>
                                            <Dropdown v-model="paymentMethod" :options="paymentMethods" optionLabel="name" placeholder="Select Payment Method" class="w-full" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-600 mb-1">Payment Amount</span>
                                            <InputNumber v-model="paymentAmount" mode="currency" currency="ZAR" locale="en-ZA" :minFractionDigits="2" class="w-full" :disabled="true" />
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-6 mb-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-600 mb-1">Reference Number</span>
                                            <InputText v-model="referenceNumber" placeholder="Enter reference number" class="w-full" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-600 mb-1">Payment Date</span>
                                            <Calendar v-model="paymentDate" dateFormat="dd/mm/yy" class="w-full" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col mb-6">
                                        <span class="text-sm text-gray-600 mb-1">Notes</span>
                                        <Textarea v-model="paymentNotes" rows="3" placeholder="Add any additional notes" class="w-full" />
                                    </div>
                                </div>
                                <div class="mt-auto border-t border-gray-200 pt-6">
                                    <div class="flex justify-between gap-4">
                                        <Button label="Back" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('2')" />
                                        <Button
                                            label="Complete Order"
                                            :icon="form.processing ? 'pi pi-spin pi-spinner' : 'pi pi-shopping-cart'"
                                            @click="finalizePayment"
                                            :disabled="!isPaymentValid || form.processing"
                                            :loading="form.processing"
                                            severity="success"
                                        >
                                        </Button>
                                    </div>
                                </div>
                            </StepPanel>
                        </StepItem>
                    </Stepper>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { FilterMatchMode } from '@primevue/core/api';

interface VendorProduct {
    id: number;
    name: string;
    unit_price: number;
    tax: number;
    total: number;
    sku: string;
    stock: number;
    inventory_stock: number;
    image_url?: string;
}

interface Vendor {
    id: number;
    name: string;
}

interface CartItem extends VendorProduct {
    quantity: number;
}

interface CalculatedTotals {
    items: VendorProduct[];
    subtotal: number;
    tax: number;
    total: number;
}

const props = defineProps<{
    vendor: Vendor;
    calculatedTotals: Object,
}>();

const breadcrumbItems = ref([
    { icon: 'pi pi-truck', label: 'Vendor' },
    { label: 'Order Stock' }
]);

const activeStep = ref('1');
const purchaseOrder = ref(null);
const cart = ref<CartItem[]>([]);
const quantities = ref<{ [key: number]: number }>({});

const cartTotal = computed(() => {
    return props.calculatedTotals.items.reduce((sum, item) => {
        const quantity = quantities.value[item.id] || 0;
        return sum + item.unit_price * quantity;
    }, 0);
});

props.calculatedTotals.items.forEach(product => {
    quantities.value[product.id] = 0;
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const form = useForm({
    purchase_order_id: null,
    items: [] as { product_id: number; quantity: number; price: number }[],
    total: 0,
    vendor_id: props.vendor.id,
    payment_method: '',
    payment_amount: 0,
    reference_number: '',
    payment_date: null,
    payment_notes: '',
});

const createPurchaseOrder = (event: Event, activateCallback: (step: string) => void) => {
    cart.value = [];
    props.calculatedTotals.items.forEach(item => {
        const quantity = quantities.value[item.id] || 0;
        if (quantity > 0) {
            cart.value.push({
                ...item,
                quantity: quantity
            });
        }
    });

    if (cart.value.length > 0) {
        purchaseOrder.value = {
            order_number: `PO-${Date.now()}`,
            vendor: { name: props.vendor.name },
            order_date: new Date().toISOString(),
            status: 'Pending',
            items: [...cart.value],
            total: cartTotal.value
        };
        activateCallback('2');
    } else {
        console.error('No items selected for purchase order');
    }
};

const calculateTotalItems = () => {
    return Object.values(quantities.value).reduce((sum, quantity) => sum + quantity, 0);
};

// Function to format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(value);
};

const paymentMethods = ref([
    { name: 'Bank Transfer', code: 'BT' },
    { name: 'Credit Card', code: 'CC' },
    { name: 'Cash', code: 'CASH' },
]);

const paymentMethod = ref(null);
const paymentAmount = computed(() => purchaseOrder.value ? purchaseOrder.value.total : 0);
const referenceNumber = computed(() => {
    return purchaseOrder.value ? purchaseOrder.value.order_number : '';
});
const paymentDate = ref(new Date());
const paymentNotes = ref('');

const isPaymentValid = computed(() => {
    return paymentMethod.value && referenceNumber.value.trim() !== '' && paymentDate.value;
});

const generateFakeReferenceNumber = () => {
    return 'FAKE-' + Math.random().toString(36).substr(2, 9).toUpperCase();
};


const finalizePayment = () => {
    if (!isPaymentValid.value || form.processing) return;

    const items = cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.unit_price,
    }));

    form.purchase_order_id = purchaseOrder.value?.id; // Use optional chaining
    form.items = items;
    form.total = cartTotal.value;
    form.vendor_id = props.vendor.id;
    form.status = 'pending';
    form.payment_method = paymentMethod.value?.code; // Use optional chaining
    form.payment_amount = paymentAmount.value;
    form.reference_number = referenceNumber.value;
    form.payment_date = paymentDate.value.toISOString().split('T')[0];
    form.payment_notes = paymentNotes.value;

    form.post(route('vendors.purchases.store', props.vendor.id), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Check if response.purchaseOrder exists
            if (response.purchaseOrder) {
                purchaseOrder.value = response.purchaseOrder;
            }
            cart.value = [];
            quantities.value = {};
            paymentMethod.value = null;
            referenceNumber.value = '';
            paymentDate.value = new Date();
            paymentNotes.value = '';
            activeStep.value = '1';
        },
        onError: (errors) => {
            console.error('Error finalizing purchase order:', errors);
        },
    });
};

onMounted(() => {
    paymentMethod.value = paymentMethods.value[0];
    referenceNumber.value = generateFakeReferenceNumber();
    paymentNotes.value = 'This is a simulated payment for demonstration purposes.';
});
</script>

<style>
.p-stepitem .p-steppanel-content {
    margin-inline-start: 0 !important;
}
</style>
