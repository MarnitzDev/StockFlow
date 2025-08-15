<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase History</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <div class="w-80">
                            <span class="p-input-icon-left w-full">
                                <InputText v-model="filters['global'].value" placeholder="Search Purchase Orders..." class="w-full" />
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
                        :value="purchaseOrders.data"
                        :paginator="true"
                        :rows="purchaseOrders.per_page"
                        :totalRecords="purchaseOrders.total"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        :lazy="true"
                        @page="onPage($event)"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
                        :filters="filters"
                        filterDisplay="menu"
                        :globalFilterFields="['order_number', 'vendor', 'order_date', 'status']"
                        tableStyle="min-width: 50rem"
                        responsiveLayout="scroll"
                    >
                        <Column field="order_number" header="Order Number" sortable></Column>
                        <Column field="vendor" header="Supplier" sortable></Column>
                        <Column field="order_date" header="Date" sortable></Column>
                        <Column field="total_amount" header="Total Amount" sortable>
                            <template #body="slotProps">
                                {{ formatCurrency(slotProps.data.total_amount) }}
                            </template>
                        </Column>
                        <Column field="status" header="Status" sortable></Column>
                        <Column field="items_count" header="Items" sortable></Column>
                        <Column header="Actions" style="width: 100px">
                            <template #body="slotProps">
                                <Link v-if="slotProps.data.id" :href="route('vendors.purchases.show', { purchaseOrder: slotProps.data.id })"
                                      class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </Link>
                            </template>
                        </Column>
                    </DataTable>
                    <p v-if="purchaseOrders.data.length === 0" class="text-center mt-4">No purchase orders found.</p>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    purchaseOrders: Object,
});

const form = useForm();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const onPage = (event) => {
    form.get(route('vendors.purchases.index'), {
        page: event.page + 1,
        perPage: event.rows,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatCurrency = (value) => {
    const number = parseFloat(value);
    return isNaN(number) ? '$0.00' : new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(number);
};
</script>
