<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useForm } from '@inertiajs/vue3';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const { formatCurrency } = useCurrencyFormatter();
const confirm = useConfirm();
const toast = useToast();

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

const updatePOSAvailability = (item) => {
    const newValue = !item.available_on_pos;
    const action = newValue ? 'show' : 'hide';
    confirm.require({
        message: `Are you sure you want to ${action} this item on POS?`,
        header: 'Confirm Action',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            useForm({
                available_on_pos: newValue
            }).put(route('inventory.updatePOSAvailability', item.id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('POS availability updated successfully');
                    // Update the item's value and trigger a re-render
                    item.available_on_pos = newValue;
                    nextTick(() => {
                        // Force a re-render of the component
                        props.items.splice(props.items.indexOf(item), 1, {...item});
                    });
                    // Show success toast
                    toast.add({
                        severity: 'success',
                        summary: 'POS Availability Updated',
                        detail: `"${item.name}" is now ${newValue ? 'visible' : 'hidden'} on POS`,
                        life: 5000
                    });
                },
                onError: (errors) => {
                    console.error('Error updating POS availability:', errors);
                    // Show error toast
                    toast.add({
                        severity: 'error',
                        summary: 'Update Failed',
                        detail: 'Failed to update POS availability',
                        life: 3000
                    });
                }
            });
        },
        reject: () => {
            // Do nothing, the switch will remain in its original state
        }
    });
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
        <ConfirmDialog />
        <Toast />
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
                            sortField="name"
                            :sortOrder="1"
                            :globalFilterFields="['name', 'sku', 'category.name', 'stock', 'price']"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="text-xl font-bold">Inventory Items</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search All" />
                                    </IconField>
                                </div>
                            </template>
                            <template #empty> No inventory items found. </template>
                            <template #loading> Loading inventory data. Please wait. </template>

                            <Column field="available_on_pos" sortable style="width: 5%;">
                                <template #header>
                                    <div class="flex items-center">
                                        <i class="pi pi-info-circle text-sm text-gray-500"
                                           v-tooltip.bottom="'Toggle item visibility on Point of Sale'"
                                        ></i>
                                        <span class="font-bold ml-2">POS</span>
                                    </div>
                                </template>
                                <template #body="{ data }">
                                    <InputSwitch
                                        :modelValue="data.available_on_pos"
                                        @click.prevent="updatePOSAvailability(data)"
                                    />
                                </template>
                            </Column>

                            <Column field="name" header="Name" sortable style="width: 30%;">
                                <template #body="{ data }">
                                    {{ data.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by name" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="sku" header="SKU" sortable style="width: 20%;">
                                <template #body="{ data }">
                                    {{ data.sku }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by SKU" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="category.name" header="Category" sortable style="width: 20%;">
                                <template #body="{ data }">
                                    {{ data.category.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by category" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="stock" dataType="numeric" sortable style="width: 6%;">
                                <template #header>
                                    <div class="flex items-center">
                                        <i class="pi pi-info-circle text-sm text-gray-500"
                                           v-tooltip.bottom="'Current stock level. Green indicates sufficient stock, red indicates low stock.'"
                                        ></i>
                                        <span class="font-bold ml-2">Stock</span>
                                    </div>
                                </template>
                                <template #body="{ data }">
                                    <Tag :value="data.stock" :severity="getSeverity(data.stock, data.low_stock_threshold)" />
                                </template>
                            </Column>

                            <Column field="price" header="Price" dataType="numeric" sortable style="width: 9%;">
                                <template #body="{ data }">
                                    {{ formatCurrency(data.price) }}
                                </template>
                            </Column>

                            <Column header="Actions" :exportable="false" style="width: 10%;">
                                <template #body="{ data }">
                                    <Button icon="pi pi-plus" outlined rounded class="mr-2" @click="openStockMovementDialog(data)" />
                                    <!--                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(data)" />-->
                                    <!--                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteItem(data)" />-->
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
