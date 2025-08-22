<template>
    <Head title="Suppliers" />
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <Link :href="route('suppliers.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Supplier
                            </Link>
                        </div>
                        <DataTable
                            :value="suppliers"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
                            responsiveLayout="scroll"
                            dataKey="id"
                        >
                            <Column field="name" header="Name" sortable>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by name" />
                                </template>
                            </Column>
                            <Column field="email" header="Email" sortable>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by email" />
                                </template>
                            </Column>
                            <Column field="phone" header="Phone" sortable>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by phone" />
                                </template>
                            </Column>
                            <Column field="address" header="Address" sortable>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @input="filterCallback()" class="p-column-filter" placeholder="Search by address" />
                                </template>
                            </Column>
                            <Column header="Actions">
                                <template #body="slotProps">
                                    <Link :href="route('suppliers.edit', slotProps.data.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</Link>
                                    <Link :href="route('suppliers.destroy', slotProps.data.id)" method="delete" as="button" class="text-red-600 hover:text-red-900" preserve-scroll>Delete</Link>
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
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';

interface Props {
    suppliers: Array<{
        id: number;
        name: string;
        email: string;
        phone: string;
        address: string;
    }>;
}

const props = defineProps<Props>();

const suppliers = ref(props.suppliers);
const filters = ref({});
</script>
