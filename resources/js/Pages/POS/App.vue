<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    products: Array,
});

const cart = ref([]);
const form = useForm({
    items: [],
    total: 0,
});

const isCartEmpty = computed(() => cart.value.length === 0);

const addToCart = (product) => {
    const quantity = product.quantity || 1;
    const availableQuantity = product.available_quantity;

    if (quantity > availableQuantity) {
        alert(`Sorry, only ${availableQuantity} item(s) available.`);
        return;
    }

    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        const newQuantity = existingItem.quantity + quantity;
        if (newQuantity > availableQuantity) {
            alert(`Sorry, you can't add more. Only ${availableQuantity - existingItem.quantity} item(s) left.`);
            return;
        }
        existingItem.quantity = newQuantity;
    } else {
        cart.value.push({ ...product, quantity });
    }

    product.available_quantity -= quantity;
    product.quantity = 1;
    updateForm();
};

const removeFromCart = (product) => {
    const index = cart.value.findIndex(item => item.id === product.id);
    if (index !== -1) {
        const originalProduct = props.products.find(p => p.id === product.id);
        if (cart.value[index].quantity > 1) {
            cart.value[index].quantity -= 1;
            originalProduct.available_quantity += 1;
        } else {
            originalProduct.available_quantity += cart.value[index].quantity;
            cart.value.splice(index, 1);
        }
    }
    updateForm();
};

const updateForm = () => {
    form.items = cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.price,
    }));
    form.total = cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
};

const checkout = () => {
    form.post(route('pos.checkout'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            cart.value = [];
            updateForm();
        },
    });
};

const cartTotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0).toFixed(2);
});
</script>

<template>
    <Head title="StockFlow POS" />

    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
<!--                            <Link :href="route('welcome')">-->
<!--                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />-->
<!--                            </Link>-->
                        </div>
                    </div>
                    <div class="flex items-center">
                        <Link :href="route('dashboard')" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</Link>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Point of Sale</h2>
            </div>
        </header>

        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Product Grid -->
                            <div class="col-span-2 grid grid-cols-3 gap-4">
                                <div v-for="product in products" :key="product.id" class="border p-4 rounded">
                                    <img :src="product.image" :alt="product.name" class="w-full h-32 object-cover mb-2">
                                    <h3 class="font-bold">{{ product.name }}</h3>
                                    <p>${{ Number(product.price).toFixed(2) }}</p>
                                    <p class="text-sm text-gray-600">Available: {{ product.stock }}</p>
                                    <div class="flex items-center mt-2 space-x-2">
                                        <InputNumber
                                            v-model="product.quantity"
                                            :min="1"
                                            :max="product.stock"
                                            showButtons
                                            class="w-28"
                                            size="small"
                                            :inputStyle="{ width: '2rem' }"
                                        />
                                        <Button
                                            @click="addToCart(product)"
                                            icon="pi pi-shopping-cart"
                                            size="small"
                                            :disabled="product.available_quantity === 0"
                                            class="flex-grow"
                                            severity="contrast"
                                        >
                                            Add
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart -->
                            <div class="border p-4 rounded">
                                <h2 class="text-xl font-bold mb-4">Cart</h2>
                                <div v-for="item in cart" :key="item.id" class="mb-2 flex justify-between items-center">
                                    <span>{{ item.name }} ({{ item.quantity }})</span>
                                    <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                                    <Button @click="removeFromCart(item)" severity="danger" icon="pi pi-times" size="small" variant="text" rounded ></Button>
                                </div>
                                <div class="mt-4 pt-4 border-t">
                                    <p class="font-bold">Total: ${{ cartTotal }}</p>
                                </div>
                                <Button
                                    @click="checkout"
                                    label="Checkout"
                                    icon="pi pi-shopping-cart"
                                    class="mt-4 w-full"
                                    :disabled="isCartEmpty"
                                >

                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
