<template>
    <AuthenticatedLayout>
        <template #summary>
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage your inventory items, track stock levels, and control POS availability.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-3 sm:p-4">
                        <dt class="text-xs font-medium text-gray-500 truncate">
                            Total Items
                        </dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ items.length }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-3 sm:p-4">
                        <dt class="text-xs font-medium text-gray-500 truncate">
                            Total Stock
                        </dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ calculateTotalstock }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-3 sm:p-4">
                        <dt class="text-xs font-medium text-gray-500 truncate">
                            Total Inventory Value
                        </dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ formatCurrency(calculateTotalValue) }}
                        </dd>
                    </div>
                </div>
            </div>
        </template>
        <ConfirmDialog />
        <Toast />
        <div class="pb-12">
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

                            <Column field="image" header="Image" style="width: 5%;">
                                <template #body="{ data }">
                                    <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                        <img
                                            :src="data.image_url || '/images/placeholder-item.svg'"
                                            :alt="data.name"
                                            class="w-full h-full object-cover rounded-md"
                                            @error="handleImageError"
                                        />
                                    </div>
                                </template>
                            </Column>

                            <Column field="name" header="Name" sortable style="width: 25%;">
                                <template #body="{ data }">
                                    {{ data.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by name" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="sku" header="SKU" sortable style="width: 18%;">
                                <template #body="{ data }">
                                    {{ data.sku }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by SKU" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="category.name" header="Category" sortable style="width: 17%;">
                                <template #body="{ data }">
                                    {{ data.category.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by category" class="p-column-filter w-full" />
                                </template>
                            </Column>

                            <Column field="stock" dataType="numeric" sortable style="width: 5%;">
                                <template #header>
                                    <div class="flex items-center">
                                        <i class="pi pi-info-circle text-sm text-gray-500"
                                           v-tooltip.bottom="'Current stock level. Green indicates stock above the set threshold, red indicates stock at or below the threshold.'"
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

                            <Column field="available_on_pos" sortable style="width: 11%;">
                                <template #header>
                                    <div class="flex items-center">
                                        <i class="pi pi-info-circle text-sm text-gray-500"
                                           v-tooltip.bottom="'Toggle item visibility on Point of Sale'"
                                        ></i>
                                        <span class="font-bold ml-2">POS</span>
                                    </div>
                                </template>
                                <template #body="{ data }">
                                    <ToggleButton
                                        v-model="tempStates[data.id]"
                                        @click.prevent="confirmPOSAvailabilityChange(data)"
                                        onLabel="Active"
                                        offLabel="Disabled"
                                        onIcon="pi pi-check"
                                        offIcon="pi pi-times"
                                        :disabled="data.isUpdating"
                                    />
                                </template>
                            </Column>

                            <Column header="Actions" :exportable="false" style="width: 10%;">
                                <template #body="{ data }">
                                    <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="viewItem(data)" />
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(data)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick, reactive } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useForm, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const { formatCurrency } = useCurrencyFormatter();
const confirm = useConfirm();
const toast = useToast();

const tempStates = reactive({});
const loading = ref(true);

onMounted(() => {
    loading.value = false;
    props.items.forEach(item => {
        tempStates[item.id] = item.available_on_pos;
    });
});

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
    stockMovementForm.post(route('inventory.updatePOSAvailability', selectedItem.value.id), {
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

const getSeverity = (stock, threshold) => {
    return stock > threshold ? 'success' : 'danger';
};

const confirmPOSAvailabilityChange = (item) => {
    const newValue = !tempStates[item.id];
    const action = !newValue ? 'show' : 'hide';

    confirm.require({
        message: `Are you sure you want to ${action} this item on POS?`,
        header: 'Confirm Action',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            updatePOSAvailability(item, newValue);
        },
        reject: () => {
            tempStates[item.id] = item.available_on_pos;
        },
        onHide: () => {
            tempStates[item.id] = item.available_on_pos;
        }
    });
};

const updatePOSAvailability = (item, newValue) => {
    item.isUpdating = true;

    useForm({
        available_on_pos: newValue
    }).put(route('inventory.updatePOSAvailability', item.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            console.log('POS availability updated successfully');
            item.available_on_pos = newValue;
            tempStates[item.id] = newValue;
            item.isUpdating = false;

            toast.add({
                severity: 'success',
                summary: 'POS Availability Updated',
                detail: `"${item.name}" is now ${newValue ? 'visible' : 'hidden'} on POS`,
                life: 5000
            });
        },
        onError: (errors) => {
            console.error('Error updating POS availability:', errors);
            item.isUpdating = false;
            tempStates[item.id] = item.available_on_pos; // Revert temp state on error

            toast.add({
                severity: 'error',
                summary: 'Update Failed',
                detail: 'Failed to update POS availability',
                life: 3000
            });
        }
    });
};

const viewItem = (item) => {
    router.visit(route('inventory.show', item.id));
};

const editItem = (item) => {
    router.visit(route('inventory.edit', item.id));
};

const deleteItem = (item) => {
    // Implement delete functionality
    console.log('Delete item:', item);
};

const handleImageError = (event) => {
    event.target.src = '/images/placeholder-item.svg';
};
</script>


