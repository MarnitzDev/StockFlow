<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
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

// DATA TABLE
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 bg-gray-100 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-800">Inventory Items</h3>
                    <Link :href="route('inventory.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Item
                    </Link>
                </div>
                <div class="flex flex-wrap justify-between items-center">
                    <div class="flex space-x-4 text-sm text-gray-600">
                        <span class="bg-white px-3 py-1 rounded-full shadow">Items: {{ items.length }}</span>
                        <span class="bg-white px-3 py-1 rounded-full shadow">Total Quantity: {{ calculateTotalQuantity }}</span>
                        <span class="bg-white px-3 py-1 rounded-full shadow">Total Value: ${{ calculateTotalValue }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <div class="w-80">
                        <span class="p-input-icon-left w-full">
                            <InputText v-model="filters['global'].value" placeholder="Search Inventory Items..." class="w-full" />
                        </span>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Export CSV
                            </button>
                            <button class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Print Report
                            </button>
                        </div>
                    </div>
                    <DataTable
                        :value="items"
                        :paginator="true"
                        :rows="10"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :rowsPerPageOptions="[10,20,50]"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                        :filters="filters"
                        filterDisplay="menu"
                        :globalFilterFields="['name', 'sku', 'category.name']"
                    >
                        <Column header="">
                            <template #body="slotProps">
                                <img :src="slotProps.data.primary_image?.image_path"
                                     :alt="slotProps.data.name"
                                     class="w-16 h-16 object-cover rounded"
                                     @error="handleImageError"
                                />
                            </template>
                        </Column>
                        <Column field="name" header="Item" sortable></Column>
                        <Column field="sku" header="SKU" sortable></Column>
                        <Column field="quantity" header="Quantity" sortable></Column>
                        <Column field="price" header="Price" sortable>
                            <template #body="slotProps">
                                {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(slotProps.data.price) }}
                            </template>
                        </Column>
                        <Column field="category.name" header="Category" sortable></Column>
                        <Column field="low_stock_threshold" header="Stock Threshold" sortable></Column>
                        <Column header="Actions">
                            <template #body="slotProps">
                                <Button label="Update Stock" icon="pi pi-plus" @click="openStockMovementDialog(slotProps.data)" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
