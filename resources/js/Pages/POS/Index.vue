<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import POSLayout from '@/Layouts/POSLayout.vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import { useDialog } from 'primevue/usedialog';
import Button from "primevue/button";

const dialog = useDialog();
const visible = ref(false);

const POSHelpContent = {
    template: `
        <div class="p-6 bg-gray-50 rounded-lg">
            <ol class="list-decimal pl-5 space-y-4">
                <li class="text-gray-700">Browse through the available products on the <span class="font-semibold text-blue-600">left side</span> of the screen.</li>
                <li class="text-gray-700">Click <span class="inline-block bg-blue-500 text-white px-2 py-1 rounded text-sm font-semibold">Add to Cart</span> to add items to your order.</li>
                <li class="text-gray-700">Adjust quantities in the cart on the <span class="font-semibold text-blue-600">right side</span> using the <span class="inline-block bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm font-semibold">+ / -</span> buttons.</li>
                <li class="text-gray-700">Review your order summary at the bottom of the cart.</li>
                <li class="text-gray-700">Click <span class="inline-block bg-green-500 text-white px-2 py-1 rounded text-sm font-semibold">Proceed to Checkout</span> when you're ready to complete the sale.</li>
            </ol>
        </div>
    `
};

const showHelp = () => {
    visible.value = true;
};

const { formatCurrency } = useCurrencyFormatter();

const props = defineProps({
    products: Array,
});

const availableProducts = ref(props.products.filter(product => product.available_on_pos).map(product => ({
    ...product,
    quantity: 1,
    originalStock: product.stock
})));

const cart = ref([]);
const form = useForm({
    items: [],
    total: 0,
});

const breadcrumbItems = ref([
    { icon: 'pi pi-shopping-cart', label: 'POS' },
    { label: 'Purchase Items' }
]);

const loremIpsumText = `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.`;

const isCartEmpty = computed(() => cart.value.length === 0);

const cartTotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

const isInCart = (product) => {
    return cart.value.some(item => item.id === product.id);
};

const getCartQuantity = (product) => {
    const cartItem = cart.value.find(item => item.id === product.id);
    return cartItem ? cartItem.quantity : 0;
};

const getRemainingStock = (product) => {
    const cartItem = cart.value.find(item => item.id === product.id);
    const cartQuantity = cartItem ? cartItem.quantity : 0;
    return product.originalStock - cartQuantity;
};

const addToCart = (product) => {
    const remainingStock = getRemainingStock(product);

    if (remainingStock === 0) {
        alert('This product is out of stock.');
        return;
    }

    const quantityToAdd = Math.min(product.quantity, remainingStock);

    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        existingItem.quantity += quantityToAdd;
    } else {
        cart.value.push({ ...product, quantity: quantityToAdd });
    }

    product.stock -= quantityToAdd;
    product.quantity = Math.min(product.quantity, product.stock);
    updateForm();
};

const updateCartItemQuantity = (item, newQuantity) => {
    const availableProduct = availableProducts.value.find(p => p.id === item.id);
    const oldQuantity = item.quantity;
    const quantityDifference = newQuantity - oldQuantity;

    if (quantityDifference > 0 && quantityDifference <= availableProduct.stock) {
        item.quantity = newQuantity;
        availableProduct.stock -= quantityDifference;
    } else if (quantityDifference < 0) {
        item.quantity = newQuantity;
        availableProduct.stock += Math.abs(quantityDifference);
    }

    if (item.quantity === 0) {
        removeFromCart(item);
    } else {
        updateForm();
    }
};

const removeFromCart = (product) => {
    const index = cart.value.findIndex(item => item.id === product.id);
    if (index !== -1) {
        const cartItem = cart.value[index];
        const availableProduct = availableProducts.value.find(p => p.id === product.id);
        availableProduct.stock += cartItem.quantity;
        cart.value.splice(index, 1);
    }
    updateForm();
};

const updateForm = () => {
    form.items = cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.price,
    }));
    form.total = cartTotal.value;
};

const checkout = () => {
    // Implement checkout logic here
    console.log('Checkout', form);
};

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(6);

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return availableProducts.value.slice(start, end);
});

const onPageChange = (event) => {
    currentPage.value = event.page + 1;
};

const onRowsChange = (event) => {
    itemsPerPage.value = event.rows;
    currentPage.value = 1;
};
</script>

<template>
    <Head title="StockFlow POS" />

    <POSLayout>
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
                    label="How to Use"
                    @click="showHelp"
                    class="p-button-text p-button-rounded"
                    aria-label="Help Guide"
                />
            </div>
        </template>

        <Dialog v-model:visible="visible" modal header="How to Use the StockFlow POS"  :breakpoints="{ '960px': '75vw', '640px': '90vw' }">
            <POSHelpContent />
        </Dialog>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="surface-ground">
                    <div class="grid grid-cols-12 gap-4">
                        <!-- Product Grid -->
                        <div class="col-span-12 lg:col-span-8">
                            <Paginator
                                v-model:rows="itemsPerPage"
                                :totalRecords="availableProducts.length"
                                @page="onPageChange"
                                @rows-change="onRowsChange"
                                :first="(currentPage - 1) * itemsPerPage"
                                :rowsPerPageOptions="[6, 12, 18]"
                                template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                :pageLinkSize="5"
                                class="mb-4">
                            </Paginator>
                            <div class="grid grid-cols-1 gap-4">
                                <div v-for="product in paginatedProducts" :key="product.id" class="surface-card bg-white rounded p-4">
                                    <div class="flex items-start">
                                        <div class="w-28 h-28 flex-shrink-0 rounded overflow-hidden border border-gray-300 shadow-sm">
                                            <img :src="product.image || '/images/placeholder-item.svg'"
                                                 :alt="product.name"
                                                 class="w-full h-full object-cover"
                                                 @error="$event.target.src = '/images/placeholder-item.svg'"
                                            />
                                        </div>
                                        <div class="ml-4 flex-grow">
                                            <div class="flex justify-between items-start">
                                                <span class="text-lg font-medium line-clamp-1">{{ product.name }}</span>
                                                <Rating :modelValue="product.rating" :readonly="true" :cancel="false" />
                                            </div>
                                            <div class="max-w-md">
                                                <p class="text-sm text-gray-600 mt-2 line-clamp-3">
                                                    {{ product.description || loremIpsumText }}
                                                </p>
                                            </div>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-xl font-semibold">{{ formatCurrency(product.price) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between mt-3">
                                                <div class="flex items-center">
                                                    <Button @click="addToCart(product)"
                                                            label="Add to Cart"
                                                            icon="pi pi-plus"
                                                            size="small"
                                                            :disabled="getRemainingStock(product) === 0"
                                                            v-tooltip.bottom="getRemainingStock(product) === 0 ? 'Out of stock' : ''"
                                                    >
                                                    </Button>
                                                    <Tag :value="isInCart(product) ? `In Cart (${getCartQuantity(product)}) | Available: ${getRemainingStock(product)}` : `Available: ${getRemainingStock(product)}`"
                                                         :severity="isInCart(product) ? 'info' : 'secondary'"
                                                         class="ml-2 text-xs" />
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <Button @click="viewProduct(product)"
                                                            label="View Product"
                                                            icon="pi pi-eye"
                                                            size="small"
                                                            text>
                                                    </Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart -->
                        <div class="col-span-12 lg:col-span-4">
                            <div class="bg-white sticky top-20 flex flex-col h-[calc(100vh-15rem)]">
                                <h2 class="text-xl font-bold p-4 border-b">Order Summary</h2>
                                <div class="overflow-y-auto flex-grow">
                                    <div v-if="cart.length === 0" class="flex flex-col items-center justify-center h-full text-gray-500">
                                        <i class="pi pi-shopping-cart mb-4" style="font-size: 1.5rem"></i>
                                        <p class="text-lg font-medium">Your cart is empty</p>
                                        <p class="text-sm mt-2">Add some items to get started!</p>
                                    </div>
                                    <div v-else v-for="item in cart" :key="item.id" class="flex justify-between items-start p-4 border-b">
                                        <div class="flex-grow pr-4">
                                            <span class="font-medium text-sm block mb-1">{{ item.name }}</span>
                                            <div class="text-sm text-gray-500 flex items-center mt-2">
                                                <span class="mr-2">Quantity:</span>
                                                <InputNumber v-model="item.quantity"
                                                             :min="1"
                                                             :max="getRemainingStock(item) + item.quantity"
                                                             @input="updateCartItemQuantity(item, $event)"
                                                             showButtons
                                                             size="small"
                                                             class="w-20"
                                                             :inputStyle="{ width: '2rem' }" />
                                                <Button @click="removeFromCart(item)"
                                                        icon="pi pi-trash"
                                                        severity="danger"
                                                        text
                                                        size="small"
                                                        class="ml-2" />
                                            </div>
                                        </div>
                                        <div class="w-32 flex flex-col items-end">
                                            <div class="font-medium">{{ formatCurrency(item.price * item.quantity) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t p-4">
                                    <div class="mb-4">
                                        <div class="flex justify-between py-2">
                                            <span class="font-medium">Subtotal</span>
                                            <span class="font-medium">{{ formatCurrency(cartTotal) }}</span>
                                        </div>
                                        <div class="flex justify-between py-2">
                                            <span class="font-medium">Shipping</span>
                                            <span class="font-medium">Free</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-t border-b">
                                            <span class="font-bold">Total</span>
                                            <span class="font-bold">{{ formatCurrency(cartTotal) }}</span>
                                        </div>
                                    </div>
                                    <Button
                                        @click="checkout"
                                        severity="success"
                                        label="Proceed to Checkout"
                                        icon="pi pi-shopping-cart"
                                        class="w-full"
                                        :disabled="isCartEmpty"
                                        v-tooltip.bottom="isCartEmpty ? 'Your cart is empty' : ''"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </POSLayout>
</template>
