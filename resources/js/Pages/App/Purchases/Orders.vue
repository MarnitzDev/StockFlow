<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PurchaseOrder } from '@/types';

interface Props {
    orders: PurchaseOrder[];
}

const props = defineProps<Props>();

const orders = ref<PurchaseOrder[]>([]);
const loading = ref(true);
const totalRecords = ref(0);
const filters = ref({});
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
    orders.value = props.orders;
    totalRecords.value = props.orders.length;
    loading.value = false;
};

onMounted(() => {
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

const formatCurrency = (value) => {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};

const formatDate = (value) => {
    return new Date(value).toLocaleDateString();
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
</script>

<template>
    <Head title="Purchase Orders" />

    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable
                            :value="orders.data"
                            :lazy="true"
                            :paginator="true"
                            :rows="10"
                            :totalRecords="totalRecords"
                            :loading="loading"
                            :filters="filters"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
                            responsiveLayout="scroll"
                            @page="onPage($event)"
                            @sort="onSort($event)"
                            @filter="onFilter($event)"
                        >
                            <Column field="order_number" header="Order Number" sortable>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by order number" />
                                </template>
                            </Column>
                            <Column field="vendor.name" header="Supplier" sortable>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by supplier" />
                                </template>
                            </Column>
                            <Column field="order_date" header="Date" sortable :body="(data) => formatDate(data.order_date)">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by date" />
                                </template>
                            </Column>
                            <Column field="total_amount" header="Total Amount" sortable :body="(data) => formatCurrency(data.total_amount)">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by amount" />
                                </template>
                            </Column>
                            <Column field="status" header="Status" sortable>
                                <template #body="{ data }">
                                    <span :class="'status-badge status-' + data.status.toLowerCase()">{{ data.status }}</span>
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <Dropdown v-model="filterModel.value" @change="filterCallback()" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Select a Status" class="p-column-filter" />
                                </template>
                            </Column>
                            <Column header="Actions">
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

<style scoped>
.status-badge {
    border-radius: 2px;
    padding: .25em .5rem;
    text-transform: uppercase;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: .3px;
}
.status-completed {
    background-color: #C8E6C9;
    color: #256029;
}
.status-pending {
    background-color: #FEEDAF;
    color: #8A5340;
}
.status-cancelled {
    background-color: #FFCDD2;
    color: #C63737;
}
</style>
