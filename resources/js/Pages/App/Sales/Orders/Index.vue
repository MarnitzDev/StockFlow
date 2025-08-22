<template>
    <Head title="Sales Orders" />
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
                                <h3 class="text-lg font-semibold text-gray-700">Total Revenue</h3>
                                <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalRevenue) }}</p>
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
                        <DataTable
                            v-model:filters="filters"
                            :value="salesOrders"
                            :paginator="true"
                            :rows="10"
                            dataKey="id"
                            :filters="filters"
                            filterDisplay="menu"
                            :globalFilterFields="['order_number', 'customer.name', 'total_amount', 'status']"
                            responsiveLayout="scroll"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="text-xl font-bold">Sales Orders</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                                    </IconField>
                                </div>
                            </template>

                            <Column field="order_number" header="Order Number" sortable style="width: 12%;"></Column>

                            <Column field="created_at" header="Date" sortable style="width: 13%;">
                                <template #body="slotProps">
                                    {{ formatDate(slotProps.data.created_at) }}
                                </template>
<!--                                <template #filter="{ filterModel, filterCallback }">-->
<!--                                    <div class="p-inputgroup">-->
<!--                                        <Calendar-->
<!--                                            v-model="filters.created_at.value"-->
<!--                                            dateFormat="dd/mm/yy"-->
<!--                                            @date-select="filterCallback()"-->
<!--                                            placeholder="Search by date"-->
<!--                                            class="p-column-filter"-->
<!--                                        />-->
<!--                                        <Button-->
<!--                                            icon="pi pi-times"-->
<!--                                            @click="clearDateFilter"-->
<!--                                            class="p-button-outlined"-->
<!--                                        />-->
<!--                                    </div>-->
<!--                                </template>-->
                            </Column>

                            <Column field="customer.name" header="Customer" sortable style="width: 32%;">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText
                                        v-model="filterModel.value"
                                        @input="filterCallback()"
                                        @clear="clearFilter('customer.name')"
                                        placeholder="Search customer"
                                    />
                                </template>
                            </Column>

                            <Column field="total_amount" header="Total Amount" sortable style="width: 15%;">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.total_amount) }}
                                </template>
                            </Column>

                            <Column field="status" header="Status" sortable style="width: 18%;">
                                <template #body="{ data }">
                                    <span :class="'status-badge status-' + data.status.toLowerCase()">{{ data.status }}</span>
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <div class="p-inputgroup">
                                        <Dropdown
                                            v-model="filterModel.value"
                                            @change="filterCallback()"
                                            :options="statusOptions"
                                            placeholder="Select Status"
                                            showClear
                                        />
                                    </div>
                                </template>
                            </Column>

                            <Column header="Actions" :exportable="false" style="width: 10%;">
                                <template #body="slotProps">
                                    <Link :href="route('sales.orders.show', slotProps.data.id)" class="mr-2">
                                        <Button
                                            icon="pi pi-eye"
                                            severity="info"
                                            outlined
                                            rounded
                                        />
                                    </Link>
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
import { ref, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import {Head, Link} from '@inertiajs/vue3';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const { formatCurrency } = useCurrencyFormatter();

const props = defineProps({
    salesOrders: Array,
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    order_number: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
    'customer.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    total_amount: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const statusOptions = [
    'Pending',
    'Processing',
    'Completed',
    'Cancelled'
];

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
    switch (status.toLowerCase()) {
        case 'pending':
            return 'warn';
        case 'processing':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return null;
    }
};

const clearFilter = (field) => {
    filters.value[field].value = null;
};

// Computed properties for overview data
const totalOrders = computed(() => props.salesOrders.length);
const totalRevenue = computed(() => props.salesOrders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0));
const averageOrderValue = computed(() => totalOrders.value ? totalRevenue.value / totalOrders.value : 0);
</script>
