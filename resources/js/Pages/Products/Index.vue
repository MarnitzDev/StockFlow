<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    products: Array
});

const visible = ref(false);

const form = useForm({
    name: '',
    sku: '',
    stock: 0,
    price: 0,
    category: '',
});

const submit = () => {
    form.post(route('products.store'), {
        preserveScroll: true,
        onSuccess: () => {
            visible.value = false;
            form.reset();
        },
    });
};

const calculateTotalQuantity = computed(() => {
    return props.products.reduce((total, product) => total + product.stock, 0);
});

const calculateTotalValue = computed(() => {
    return props.products.reduce((total, product) => total + (product.price * product.stock), 0).toFixed(2);
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
                <Button label="Add New Product" icon="pi pi-plus" @click="visible = true" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-4">
                        <span>Folders: 0</span>
                        <span>Items: {{ products.length }}</span>
                        <span>Total Quantity: {{ calculateTotalQuantity }}</span>
                        <span>Total Value: ${{ calculateTotalValue }}</span>
                    </div>

                    <table class="min-w-full mt-4">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">SKU</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Stock</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Price</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Category</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.sku }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.stock }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.price }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ product.category }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                <Link :href="route('products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</Link>
                                <Link :href="route('products.destroy', product.id)" method="delete" as="button" class="text-red-600 hover:text-red-900" @click="confirm('Are you sure you want to delete this product?')">Delete</Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="visible" modal header="Create New Product" :style="{ width: '50vw' }">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                    <input v-model="form.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="sku">SKU</label>
                    <input v-model="form.sku" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="sku" type="text" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="stock">Stock</label>
                    <input v-model="form.stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="stock" type="number" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Price</label>
                    <input v-model="form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="price" type="number" step="0.01" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Category</label>
                    <input v-model="form.category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category" type="text" required>
                </div>
                <div class="flex items-center justify-end">
                    <Button label="Cancel" icon="pi pi-times" @click="visible = false" class="p-button-text" />
                    <Button label="Create" icon="pi pi-check" type="submit" autofocus />
                </div>
            </form>
        </Dialog>
    </AuthenticatedLayout>
</template>
