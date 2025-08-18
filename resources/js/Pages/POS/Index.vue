<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import POSLayout from '@/Layouts/POSLayout.vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

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
</script>

<template>
    <Head title="StockFlow POS" />

    <POSLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Point of Sale</h2>
        </template>

        <div class="py-12">
            <div class="px-6">
                <div class="surface-ground">
                    <div class="grid grid-cols-12 gap-4">
                        <!-- Product Grid -->
                        <div class="col-span-12 lg:col-span-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="product in availableProducts" :key="product.id" class="surface-card bg-white shadow-lg border-round p-4 flex flex-column">
                                    <div class="relative mb-3 w-3/4">
                                        <img :src="product.image || '/images/placeholder-item.svg'"
                                             :alt="product.name"
                                             class="w-full h-40 object-cover border-round"
                                             @error="$event.target.src = '/images/placeholder-item.svg'"
                                        />
                                        <Tag :value="product.category" severity="info" class="absolute top-0 left-0 m-2" />
                                    </div>
                                    <div class="ml-4">
                                        <span class="text-lg font-medium mb-2 line-clamp-1">{{ product.name }}</span>
                                        <span class="text-sm text-500 mb-3 flex-grow-1 line-clamp-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xl font-semibold">{{ formatCurrency(product.price) }}</span>
                                            <span class="text-sm text-gray-600">Available: {{ getRemainingStock(product) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <Button @click="addToCart(product)"
                                                    label="Add to Cart"
                                                    icon="pi pi-plus"
                                                    size="small"
                                                    :disabled="getRemainingStock(product) === 0">
                                            </Button>
                                            <Tag v-if="isInCart(product)"
                                                 :value="`In Cart (${getCartQuantity(product)})`"
                                                 severity="success"
                                                 class="ml-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart -->
                        <div class="col-span-12 lg:col-span-4">
                            <div class="border p-4 rounded bg-white shadow-lg sticky top-4">
                                <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                                <div class="mb-4">
                                    <div v-for="item in cart" :key="item.id" class="flex justify-between items-center py-2 border-b">
                                        <div class="flex items-center">
                                            <img :src="item.image || '/images/placeholder-item.svg'"
                                                 :alt="item.name"
                                                 class="w-12 h-12 object-cover rounded mr-3" />
                                            <div>
                                                <span class="font-medium">{{ item.name }}</span>
                                                <div class="text-sm text-gray-500 flex items-center mt-1">
                                                    <span class="mr-2">Quantity:</span>
                                                    <InputNumber v-model="item.quantity"
                                                                 :min="1"
                                                                 :max="getRemainingStock(item) + item.quantity"
                                                                 @input="updateCartItemQuantity(item, $event)"
                                                                 showButtons
                                                                 size="small"
                                                                 class="w-20 mr-2"
                                                                 :inputStyle="{ width: '2rem' }" />
                                                    <Button @click="removeFromCart(item)"
                                                            icon="pi pi-trash"
                                                            severity="danger"
                                                            variant="outlined" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium">{{ formatCurrency(item.price * item.quantity) }}</div>
                                        </div>
                                    </div>
                                </div>
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
                                    label="Proceed to Checkout"
                                    icon="pi pi-shopping-cart"
                                    class="w-full"
                                    :disabled="isCartEmpty"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </POSLayout>
</template>
