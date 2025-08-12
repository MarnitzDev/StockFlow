<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

interface Props {
    suppliers: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            phone: string;
            address: string;
        }>;
        total: number;
    };
}

const props = defineProps<Props>();

const suppliers = ref(props.suppliers.data);
const totalRecords = ref(props.suppliers.total);
const loading = ref(false);
const filters = ref({});
const lazyParams = ref({
    first: 0,
    rows: 10,
    page: 1,
    sortField: null,
    sortOrder: null,
});

const loadLazyData = () => {
    loading.value = true;

    router.get(
        route('suppliers.index'),
        {
            page: lazyParams.value.page,
            perPage: lazyParams.value.rows,
            sortField: lazyParams.value.sortField,
            sortOrder: lazyParams.value.sortOrder,
            filters: filters.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['suppliers'],
            onSuccess: (page) => {
                suppliers.value = page.props.suppliers.data;
                totalRecords.value = page.props.suppliers.total;
                loading.value = false;
            },
        }
    );
};

onMounted(() => {
    loadLazyData();
});

const onPage = (event) => {
    lazyParams.value.page = event.page + 1;
    lazyParams.value.rows = event.rows;
    loadLazyData();
};

const onSort = (event) => {
    lazyParams.value.sortField = event.sortField;
    lazyParams.value.sortOrder = event.sortOrder;
    loadLazyData();
};

const onFilter = () => {
    lazyParams.value.page = 1;
    loadLazyData();
};
</script>

<template>
    <Head title="Suppliers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suppliers</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4">
                            <Link :href="route('suppliers.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Supplier
                            </Link>
                        </div>
                        <DataTable
                            :value="suppliers"
                            :lazy="true"
                            :paginator="true"
                            :rows="lazyParams.rows"
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
