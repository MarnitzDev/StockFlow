
<template>
    <AuthenticatedLayout>
        <div class="">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <DataTable :value="stockMovements" :paginator="true" :rows="10" :filters="filters" filterDisplay="menu" responsiveLayout="scroll">
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="text-xl font-bold">Stock Movements</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search All" />
                                    </IconField>
                                </div>
                            </template>

                            <Column field="inventory.name" header="Item" :sortable="true"></Column>
                            <Column field="type" header="Type" :sortable="true">
                                <template #body="slotProps">
                                    <span :class="{'text-green-600': slotProps.data.type === 'in', 'text-red-600': slotProps.data.type === 'out'}">
                                        {{ slotProps.data.type === 'in' ? 'Stock In' : 'Stock Out' }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity" :sortable="true"></Column>
                            <Column field="inventory.stock" header="Current Stock" :sortable="true"></Column>
                            <Column field="unit_price" header="Unit Price" :sortable="true">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.unit_price) }}
                                </template>
                            </Column>
                            <Column header="Total Value" :sortable="true">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.quantity * slotProps.data.unit_price) }}
                                </template>
                            </Column>
                            <Column field="reference" header="Order Reference" :sortable="true">
                                <template #body="slotProps">
                                    {{ slotProps.data.reference || 'N/A' }}
                                </template>
                            </Column>
                            <Column field="created_at" header="Date" :sortable="true">
                                <template #body="slotProps">
                                    {{ formatDate(slotProps.data.created_at) }}
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
import { ref } from 'vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import InputText from 'primevue/inputtext';

const { formatCurrency } = useCurrencyFormatter();

const props = defineProps({
    stockMovements: Array
});

const filters = ref({
    'global': { value: null, matchMode: 'contains' }
});

const formatDate = (dateString) => {
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };
    return new Date(dateString).toLocaleString('en-US', options);
};
</script>
