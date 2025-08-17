<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useForm } from '@inertiajs/vue3';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const { formatCurrency } = useCurrencyFormatter();

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
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'category.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    sku: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const loading = ref(true);

onMounted(() => {
    loading.value = false;
});

const getSeverity = (stock, threshold) => {
    return stock > threshold ? 'success' : 'danger';
};

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
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable
                            v-model:filters="filters"
                            :value="items"
                            paginator
                            :rows="10"
                            dataKey="id"
                            filterDisplay="row"
                            :loading="loading"
                            :globalFilterFields="['name', 'sku', 'category.name', 'stock', 'price']"
                        >
                            <template #header>
                                <div class="flex justify-end">
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                                    </IconField>
                                </div>
                            </template>
                            <template #empty> No inventory items found. </template>
                            <template #loading> Loading inventory data. Please wait. </template>

                            <Column field="name" header="Name" style="min-width: 12rem">
                                <template #body="{ data }">
                                    {{ data.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by name" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="sku" header="SKU" style="min-width: 12rem">
                                <template #body="{ data }">
                                    {{ data.sku }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by SKU" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="category.name" header="Category" style="min-width: 12rem">
                                <template #body="{ data }">
                                    {{ data.category.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by category" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="stock" header="Stock" dataType="numeric" style="min-width: 8rem">
                                <template #body="{ data }">
                                    <Tag :value="data.stock" :severity="getSeverity(data.stock, data.low_stock_threshold)" />
                                </template>
                            </Column>

                            <Column field="price" header="Price" dataType="numeric" style="min-width: 8rem">
                                <template #body="{ data }">
                                    {{ formatCurrency(data.price) }}
                                </template>
                            </Column>

                            <Column header="Actions" :exportable="false" style="min-width: 8rem">
                                <template #body="{ data }">
                                    <Button icon="pi pi-plus" outlined rounded class="mr-2" @click="openStockMovementDialog(data)" />
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(data)" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteItem(data)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
