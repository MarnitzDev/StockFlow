<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FolderStructure from '@/Components/FolderStructure.vue';
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            Products
        </template>

        <div class="grid grid-cols-4 gap-6">
            <div class="col-span-1">
                <FolderStructure />
            </div>
            <div class="col-span-3">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-4">
                        <span>Folders: 0</span>
                        <span>Items: 1</span>
                        <span>Total Quantity: 5 gal</span>
                        <span>Total Value: $0.00</span>
                    </div>
                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                <Link :href="route('products.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Add New Product
                                </Link>

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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
