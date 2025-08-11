<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import SupplierLayout from '@/Layouts/SupplierLayout.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

interface Product {
    id: number;
    name: string;
    price: number;
    sku: string;
    stock: number;
    category: string | null;
    image: string | null;
}

interface Supplier {
    id: number;
    name: string;
}

interface CartItem extends Product {
    quantity: number;
}

const props = defineProps<{
    supplier: Supplier;
    products: Product[];
}>();

const cart = ref<CartItem[]>([]);
const quantities = ref<{ [key: number]: number }>({});

props.products.forEach(product => {
    quantities.value[product.id] = 1;
});

const form = useForm({
    items: [] as { product_id: number; quantity: number; price: number }[],
    total: 0,
    supplier_id: props.supplier.id,
});

const addToCart = (product: Product) => {
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

const removeFromCart = (product: Product) => {
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
        price: item.price,
    }));
    form.total = cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
};

const checkout = () => {
    form.post(route('supplier.purchase.checkout'), {
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

const cartTotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0).toFixed(2);
});

const selectedProduct = ref<Product | null>(null);

const showProductDetails = (product: Product) => {
    selectedProduct.value = product;
};
</script>

<template>
    <SupplierLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase from {{ supplier.name }}</h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 flex">
                <!-- Product Table -->
                <div class="flex-grow mr-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" :src="product.image" :alt="product.name">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 cursor-pointer hover:text-indigo-600" @click="showProductDetails(product)">
                                            {{ product.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ product.category }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ product.price.toFixed(2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input
                                    type="number"
                                    v-model="quantities[product.id]"
                                    min="1"
                                    :max="product.stock"
                                    class="w-20 px-2 py-1 border rounded"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="addToCart(product)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    :disabled="!quantities[product.id] || quantities[product.id] < 1"
                                >
                                    Add to Cart
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Purchase Cart -->
                <div class="w-1/3 bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-bold mb-4">Purchase Order</h2>
                    <div v-for="item in cart" :key="item.id" class="mb-2 flex justify-between items-center">
                        <span class="text-sm">{{ item.name }} ({{ item.quantity }})</span>
                        <div>
                            <span class="text-sm mr-2">${{ (item.price * item.quantity).toFixed(2) }}</span>
                            <button @click="removeFromCart(item)" class="text-red-500 text-sm">Remove</button>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <p class="font-bold">Total: ${{ cartTotal }}</p>
                    </div>
                    <button @click="checkout" class="mt-4 w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors duration-300" :disabled="cart.length === 0">
                        Create Purchase Order
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Details Modal -->
        <div v-if="selectedProduct" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">{{ selectedProduct.name }}</h3>
                    <button @click="selectedProduct = null" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-48 object-cover mb-4 rounded">
                <p class="mb-2"><strong>SKU:</strong> {{ selectedProduct.sku }}</p>
                <p class="mb-2"><strong>Price:</strong> ${{ selectedProduct.price.toFixed(2) }}</p>
                <p class="mb-2"><strong>Stock:</strong> {{ selectedProduct.stock }}</p>
                <p class="mb-2"><strong>Category:</strong> {{ selectedProduct.category || 'N/A' }}</p>
                <div class="mt-4 flex justify-between items-center">
                    <input
                        type="number"
                        v-model="quantities[selectedProduct.id]"
                        min="1"
                        :max="selectedProduct.stock"
                        class="w-20 px-2 py-1 border rounded"
                    >
                    <button
                        @click="addToCart(selectedProduct); selectedProduct = null;"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors duration-300"
                        :disabled="!quantities[selectedProduct.id] || quantities[selectedProduct.id] < 1"
                    >
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </SupplierLayout>
</template>
