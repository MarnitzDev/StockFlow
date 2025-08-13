<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref } from 'vue';

const props = defineProps({
    categories: Array
});

const expandedRows = ref({});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(value);
};

const onRowExpand = (event) => {
    // You can add any logic here that needs to run when a row is expanded
    console.log('Row expanded:', event.data);
};

const onRowCollapse = (event) => {
    // You can add any logic here that needs to run when a row is collapsed
    console.log('Row collapsed:', event.data);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Inventory Categories</h3>
                        <Link :href="route('inventory.categories.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Category
                        </Link>
                    </div>
                    <DataTable
                        :value="categories"
                        v-model:expandedRows="expandedRows"
                        @row-expand="onRowExpand"
                        @row-collapse="onRowCollapse"
                        dataKey="id"
                        class="p-datatable-sm"
                    >
                        <Column :expander="true" headerStyle="width: 3rem" />
                        <Column field="name" header="Category Name" sortable></Column>
                        <Column field="slug" header="Slug" sortable></Column>
                        <Column header="Actions">
                            <template #body="slotProps">
                                <Link :href="route('inventory.categories.edit', slotProps.data.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</Link>
                                <!-- Add delete functionality here -->
                            </template>
                        </Column>
                        <template #expansion="slotProps">
                            <div class="p-3">
                                <DataTable :value="slotProps.data.inventory_items" class="p-datatable-sm" :showHeaders="slotProps.data.inventory_items.length > 0">
                                    <template #empty>
                                        <div class="p-4">
                                            No items found in this category.
                                        </div>
                                    </template>
                                    <Column field="name" header="Name"></Column>
                                    <Column field="sku" header="SKU"></Column>
                                    <Column field="stock" header="Stock"></Column>
                                    <Column field="price" header="Price">
                                        <template #body="itemProps">
                                            {{ formatCurrency(itemProps.data.price) }}
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
