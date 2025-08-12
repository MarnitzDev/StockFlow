<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase History</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable :value="purchaseOrders.data" :paginator="true" :rows="purchaseOrders.per_page"
                                   :totalRecords="purchaseOrders.total"
                                   :rowsPerPageOptions="[5, 10, 20, 50]"
                                   :lazy="true"
                                   @page="onPage($event)"
                                   paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                                   currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
                                   responsiveLayout="scroll">
                            <Column field="order_number" header="Order Number" sortable></Column>
                            <Column field="vendor" header="Supplier" sortable></Column>
                            <Column field="order_date" header="Date" sortable></Column>
                            <Column field="total_amount" header="Total Amount" sortable>
                                <template #body="slotProps">
                                    ${{ typeof slotProps.data.total_amount === 'number' ? slotProps.data.total_amount.toFixed(2) : parseFloat(slotProps.data.total_amount || 0).toFixed(2) }}
                                </template>
                            </Column>
                            <Column field="status" header="Status" sortable></Column>
                            <Column field="items_count" header="Items" sortable></Column>
                            <Column header="Actions">
                                <template #body="slotProps">
                                    <Link v-if="slotProps.data.id" :href="route('vendor.purchases.show', { purchaseOrder: slotProps.data.id })"
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
        </div>
    </VendorLayout>
</template>

<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    purchaseOrders: Object,
});

const form = useForm();

const onPage = (event) => {
    form.get(route('vendor.purchases.history'), {
        page: event.page + 1,
        perPage: event.rows,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
