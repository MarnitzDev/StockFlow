<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
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
    calculatedTotals: CalculatedTotals;
}>();

const cart = ref<CartItem[]>([]);
const quantities = ref<{ [key: number]: number }>({});

props.calculatedTotals.items.forEach(product => {
    quantities.value[product.id] = 1;
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const form = useForm({
    items: [] as { product_id: number; quantity: number; price: number }[],
    total: 0,
    vendor_id: props.vendor.id,
});

const addToCart = (product: VendorProduct) => {
    const quantity = quantities.value[product.id];
    if (!quantity || quantity < 1) return;

    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.value.push({ ...product, quantity });
    }
    quantities.value[product.id] = 1;
    updateForm();
};

const removeFromCart = (product: VendorProduct) => {
    const index = cart.value.findIndex(item => item.id === product.id);
    if (index !== -1) {
        if (cart.value[index].quantity > 1) {
            cart.value[index].quantity -= 1;
        } else {
            cart.value.splice(index, 1);
        }
    }
    updateForm();
};

const updateForm = () => {
    form.items = cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.unit_price,
    }));
    form.total = cart.value.reduce((sum, item) => sum + item.total * item.quantity, 0);
};

const checkout = () => {
    form.post(route('vendor.purchases.checkout'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            cart.value = [];
            updateForm();
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const cartSubtotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.unit_price * item.quantity, 0);
});

const cartTax = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.tax * item.quantity, 0);
});

const cartTotal = computed(() => {
    return cartSubtotal.value + cartTax.value;
});

const selectedProduct = ref<VendorProduct | null>(null);

const showProductDetails = (product: VendorProduct) => {
    selectedProduct.value = product;
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(value);
};


</script>

<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase from {{ vendor.name }}</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Product Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable
                            :value="calculatedTotals.items"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            filterDisplay="menu"
                            :globalFilterFields="['name', 'sku', 'unit_price', 'stock']"
                            responsiveLayout="scroll"
                            size="small"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0">Inventory Items</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                                    </IconField>
                                </div>
                            </template>

                            <Column field="name" header="Product" sortable>
                                <template #body="slotProps">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 cursor-pointer hover:text-indigo-600" @click="showProductDetails(slotProps.data)">
                                            {{ slotProps.data.name }}
                                        </span>
                                    </div>
                                </template>
                            </Column>
                            <Column field="sku" header="SKU" sortable></Column>
                            <Column field="total" header="Unit Price" sortable>
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.total) }}
                                </template>
                            </Column>
                            <Column field="stock" header="Stock" sortable>
                                <template #body="slotProps">
                                    <span>
                                        {{ slotProps.data.stock }}
                                    </span>
                                </template>
                            </Column>
                            <Column header="Quantity">
                                <template #body="slotProps">
                                    <InputNumber
                                        v-model="quantities[slotProps.data.id]"
                                        :min="1"
                                        :max="slotProps.data.stock"
                                        showButtons
                                        class="w-20"
                                        size="small"
                                        :inputStyle="{ width: '2rem' }"
                                    />
                                </template>
                            </Column>
                            <Column header="Actions">
                                <template #body="slotProps">
                                    <Button
                                        @click="addToCart(slotProps.data)"
                                        label="Add"
                                        icon="pi pi-plus"
                                        variant="text"
                                        severity="success"
                                        :disabled="!quantities[slotProps.data.id] || quantities[slotProps.data.id] < 1"
                                    >
                                    </Button>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
                <!-- Cart Summary at the top -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg my-6">
                    <div class="p-6 bg-gray-50">
                        <h2 class="text-xl font-bold mb-4">Purchase Order Summary</h2>
                        <div v-if="cart.length > 0">
                            <div class="mb-4 max-h-60 overflow-y-auto">
                                <div v-for="item in cart" :key="item.id" class="flex justify-between items-center py-2 border-b">
                                    <div>
                                        <span class="font-medium">{{ item.name }}</span>
                                        <span class="text-sm text-gray-600 ml-2">(Qty: {{ item.quantity }})</span>
                                    </div>
                                    <div>
                                        <span class="text-sm mr-2">{{ formatCurrency(item.unit_price * item.quantity) }}</span>
                                        <Button
                                            @click="removeFromCart(item)"
                                            icon="pi pi-trash"
                                            class="p-button-text p-button-rounded p-button-danger"
                                            aria-label="Remove item"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm">Subtotal:</span>
                                    <span>{{ formatCurrency(cartSubtotal) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm">Tax:</span>
                                    <span>{{ formatCurrency(cartTax) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="font-bold">Total:</span>
                                    <span class="font-bold text-lg">{{ formatCurrency(cartTotal) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Total Items: {{ cart.reduce((sum, item) => sum + item.quantity, 0) }}</span>
                                    <Button
                                        @click="checkout"
                                        label="Create Purchase Order"
                                        icon="pi pi-shopping-cart"
                                        class="p-button-primary"
                                        :disabled="cart.length === 0"
                                    />
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">
                            Your cart is empty. Add some products to create a purchase order.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </VendorLayout>
</template>
