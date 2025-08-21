<template>
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <!-- Overview Section -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex items-center">
                            <div class="flex-shrink-0 bg-indigo-100 rounded-full p-3 mr-4 w-10 h-10 flex items-center justify-center">
                                <i class="pi pi-shopping-cart text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">Total Orders</h3>
                                <p class="text-3xl font-bold text-indigo-600">{{ totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-full p-3 mr-4 w-10 h-10 flex items-center justify-center">
                                <i class="pi pi-dollar text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">Total Spent</h3>
                                <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalSpent) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-full p-3 mr-4 w-10 h-10 flex items-center justify-center">
                                <i class="pi pi-chart-line text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">Average Order Value</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ formatCurrency(averageOrderValue) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div v-if="orders.length === 0">No purchase orders found.</div>
                        <DataTable v-else
                                   v-model:filters="filters"
                                   :value="orders"
                                   :paginator="true"
                                   :rows="10"
                                   dataKey="id"
                                   :filters="filters"
                                   filterDisplay="menu"
                                   :globalFilterFields="['order_number', 'vendor.name', 'total_amount', 'status']"
                                   responsiveLayout="scroll"
                                   :loading="loading"
                                   :totalRecords="totalRecords"
                                   @page="onPage($event)"
                                   @sort="onSort($event)"
                                   @filter="onFilter($event)"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="text-xl font-bold">Purchase Orders</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                                    </IconField>
                                </div>
                            </template>

                            <Column field="order_number" header="Order Number" sortable style="width: 15%;">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by order number" />
                                </template>
                            </Column>

                            <Column field="order_date" header="Date" sortable style="width: 13%;">
                                <template #body="slotProps">
                                    {{ formatDate(slotProps.data.order_date) }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by date" />
                                </template>
                            </Column>

                            <Column field="vendor.name" header="Supplier" sortable style="width: 28%;">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by supplier" />
                                </template>
                            </Column>

                            <Column field="total_amount" header="Total Amount" sortable style="width: 15%;">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.total_amount) }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by amount" />
                                </template>
                            </Column>

                            <Column field="status" header="Status" sortable style="width: 15%;">
                                <template #body="{ data }">
                                    <span :class="'status-badge status-' + data.status.toLowerCase()">{{ data.status }}</span>
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <Dropdown v-model="filterModel.value" @change="filterCallback()" :options="statusOptions" optionLabel="label" optionValue="value" showClear placeholder="Select a Status" class="p-column-filter" />
                                </template>
                            </Column>

                            <Column header="Actions" :exportable="false" style="width: 15%;">
                                <template #body="{ data }">
                                    <Button icon="pi pi-eye" class="p-button-rounded p-button-text" @click="viewOrder(data)" />
                                    <Button icon="pi pi-pencil" class="p-button-rounded p-button-text" @click="editOrder(data)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PurchaseOrder } from '@/types';
import { FilterMatchMode } from '@primevue/core/api';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const { formatCurrency } = useCurrencyFormatter();

interface Props {
    orders: {
        data: PurchaseOrder[];
    };
}

const props = defineProps<Props>();

const orders = ref<PurchaseOrder[]>([]);
const loading = ref(true);
const totalRecords = ref(0);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    order_number: { value: null, matchMode: FilterMatchMode.CONTAINS },
    order_date: { value: null, matchMode: FilterMatchMode.DATE_IS },
    'vendor.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    total_amount: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS }
});
const lazyParams = ref({
    first: 0,
    rows: 10,
    page: 1,
    sortField: null,
    sortOrder: null,
});

const statusOptions = [
    { label: 'Completed', value: 'completed' },
    { label: 'Pending', value: 'pending' },
    { label: 'Cancelled', value: 'cancelled' }
];

const loadLazyData = () => {
    loading.value = true;
    console.log('Loading data, props.orders:', props.orders);
    orders.value = props.orders?.data || [];
    console.log('Loaded orders:', orders.value);
    totalRecords.value = orders.value.length;
    loading.value = false;
};

onMounted(() => {
    console.log('Component mounted');
    loadLazyData();
});

const onPage = (event) => {
    lazyParams.value = event;
    loadLazyData();
};

const onSort = (event) => {
    lazyParams.value = event;
    loadLazyData();
};

const onFilter = () => {
    lazyParams.value.first = 0;
    loadLazyData();
};

const formatDate = (value) => {
    if (value && !isNaN(new Date(value))) {
        return new Date(value).toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }
    return '';
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'cancelled':
            return 'danger';
        default:
            return null;
    }
};

const viewOrder = (data: PurchaseOrder) => {
    router.visit(route('purchases.show', { id: data.id }));
};

const editOrder = (data: PurchaseOrder) => {
    router.visit(route('purchases.edit', { id: data.id }));
};

// Computed properties for overview data
const totalOrders = computed(() => orders.value?.length || 0);
const totalSpent = computed(() => {
    if (!Array.isArray(orders.value)) return 0;
    return orders.value.reduce((sum, order) => sum + (parseFloat(order.total_amount) || 0), 0);
});
const averageOrderValue = computed(() => totalOrders.value ? totalSpent.value / totalOrders.value : 0);
</script>
