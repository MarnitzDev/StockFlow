<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
});

const calculateTotalQuantity = computed(() => {
    return props.items.reduce((total, item) => total + item.quantity, 0);
});

const calculateTotalValue = computed(() => {
    return props.items.reduce((total, item) => {
        return total + (item.quantity * parseFloat(item.price));
    }, 0).toFixed(2);
});

// STOCK

const stockMovementDialog = ref(false);
const selectedItem = ref(null);
const stockMovementForm = useForm({
    quantity: 0,
    type: 'in',
    reason: '',
});

const openStockMovementDialog = (item) => {
    selectedItem.value = item;
    stockMovementDialog.value = true;
};

const submitStockMovement = () => {
    console.log('Submitting stock movement:', stockMovementForm);
    stockMovementForm.post(route('inventory.updateStock', selectedItem.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Stock movement submitted successfully');
            stockMovementDialog.value = false;
            stockMovementForm.reset();
        },
        onError: (errors) => {
            console.error('Error submitting stock movement:', errors);
        }
    });
};

</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Items</h2>
                <Link :href="route('inventory.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Item
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-4">
                        <span>Items: {{ items.length }}</span>
                        <span>Total Quantity: {{ calculateTotalQuantity }}</span>
                        <span>Total Value: ${{ calculateTotalValue }}</span>
                    </div>

                    <table class="min-w-full mt-4">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">SKU</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">quantity</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Price</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Category</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Stock Threshold</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in items" :key="item.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ item.name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ item.sku }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ item.quantity }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ item.price }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ item.category.name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ item.low_stock_threshold }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
<!--                                <Link :href="route('inventory.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</Link>-->
<!--                                <Link :href="route('inventory.destroy', item.id)" method="delete" as="button" class="text-red-600 hover:text-red-900" @click="confirm('Are you sure you want to delete this item?')">Delete</Link>-->
                                <Button label="Update Stock" icon="pi pi-plus" @click="openStockMovementDialog(item)" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
