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

const calculateTotalstock = computed(() => {
    return props.items.reduce((total, item) => total + item.stock, 0);
});

const calculateTotalValue = computed(() => {
    return props.items.reduce((total, item) => {
        return total + (item.stock * parseFloat(item.price));
    }, 0).toFixed(2);
});

// STOCK
const stockMovementDialog = ref(false);
const selectedItem = ref(null);
const stockMovementForm = useForm({
    stock: 0,
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

const editItem = (item) => {
    // Implement edit functionality
    console.log('Edit item:', item);
};

const deleteItem = (item) => {
    // Implement delete functionality
    console.log('Delete item:', item);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Items</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable
                            :value="items"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            filterDisplay="menu"
                            :globalFilterFields="['name', 'sku', 'category.name', 'stock', 'price']"
                            responsiveLayout="scroll"
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

                            <Column field="name" header="Name" sortable></Column>
                            <Column header="Image">
                                <template #body="slotProps">
                                    <img :src="slotProps.data.primary_image?.image_path" :alt="slotProps.data.name" class="w-12 shadow-2 rounded" />
                                </template>
                            </Column>
                            <Column field="sku" header="SKU" sortable></Column>
                            <Column field="category.name" header="Category" sortable></Column>
                            <Column field="stock" header="Stock" sortable>
                                <template #body="slotProps">
                                    <span :class="{'text-green-500': slotProps.data.stock > slotProps.data.low_stock_threshold, 'text-red-500': slotProps.data.stock <= slotProps.data.low_stock_threshold}">
                                        {{ slotProps.data.stock }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="price" header="Price" sortable>
                                <template #body="slotProps">
<!--                                    {{ formatCurrency(slotProps.data.price) }}-->
                                </template>
                            </Column>
                            <Column header="Actions">
                                <template #body="slotProps">
                                    <Button icon="pi pi-plus" outlined rounded class="mr-2" @click="openStockMovementDialog(slotProps.data)" />
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(slotProps.data)" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteItem(slotProps.data)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
