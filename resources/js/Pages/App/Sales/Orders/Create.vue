<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    customers: Array,
    inventoryItems: Array,
});

const form = useForm({
    customer_id: '',
    items: [{ inventory_id: '', quantity: 1 }],
    notes: ''
});

const addItem = () => {
    form.items.push({ inventory_id: '', quantity: 1 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('sales.orders.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="customer">
                                Customer
                            </label>
                            <select v-model="form.customer_id" id="customer" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select a customer</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.name }}
                                </option>
                            </select>
                        </div>

                        <div v-for="(item, index) in form.items" :key="index" class="mb-4">
                            <div class="flex items-center">
                                <div class="w-1/2 pr-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" :for="'item-'+index">
                                        Item
                                    </label>
                                    <select v-model="item.inventory_id" :id="'item-'+index" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="">Select an item</option>
                                        <option v-for="inventoryItem in inventoryItems" :key="inventoryItem.id" :value="inventoryItem.id">
                                            {{ inventoryItem.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="w-1/4 px-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" :for="'quantity-'+index">
                                        Quantity
                                    </label>
                                    <input v-model="item.quantity" :id="'quantity-'+index" type="number" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="w-1/4 pl-2 flex items-end">
                                    <button @click="removeItem(index)" type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button @click="addItem" type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Add Item
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                                Notes
                            </label>
                            <textarea v-model="form.notes" id="notes" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3"></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
