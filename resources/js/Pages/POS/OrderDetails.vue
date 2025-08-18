<template>
    <POSLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <Breadcrumb :model="breadcrumbItems" class="p-0">
                    <template #item="{ item }">
                        <span class="flex items-center">
                            <span v-if="item.icon" :class="[item.icon, 'mr-2 text-gray-500']"></span>
                            <span :class="{'font-semibold text-gray-700': !item.icon, 'text-gray-500': item.icon}">
                                {{ item.label }}
                            </span>
                        </span>
                    </template>
                </Breadcrumb>
            </div>
        </template>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="py-4">
                            <h3 class="text-xl font-semibold mb-2">Order #{{ order.order_number }}</h3>
                            <p class="text-sm text-gray-600">Date: {{ formatDate(order.created_at) }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="py-4">
                                <h4 class="font-medium mb-2 text-gray-700">Customer Information</h4>
                                <p><span class="font-semibold">Name:</span> {{ order.customer.name }}</p>
                                <p><span class="font-semibold">Email:</span> {{ order.customer.email }}</p>
                            </div>
                            <div class="p-4">
                                <h4 class="font-medium mb-2 text-gray-700 ">Order Summary</h4>
                                <p><span class="font-semibold">Status:</span> {{ order.status }}</p>
                                <p><span class="font-semibold">Total Amount:</span> {{ formatCurrency(order.total_amount) }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Order Items</h4>
                            <DataTable :value="order.items" responsiveLayout="scroll">
                                <Column field="inventory.name" header="Product"></Column>
                                <Column field="quantity" header="Quantity"></Column>
                                <Column field="unit_price" header="Unit Price">
                                    <template #body="slotProps">
                                        {{ formatCurrency(slotProps.data.unit_price) }}
                                    </template>
                                </Column>
                                <Column field="subtotal" header="Subtotal">
                                    <template #body="slotProps">
                                        {{ formatCurrency(slotProps.data.subtotal) }}
                                    </template>
                                </Column>
                                <ColumnGroup type="footer">
                                    <Row>
                                        <Column footer="Total:" colspan="3"></Column>
                                        <Column :footer="formatCurrency(order.total_amount)"></Column>
                                    </Row>
                                </ColumnGroup>
                            </DataTable>
                        </div>

                        <form @submit.prevent="confirmOrder" class="mt-6">
                            <div class="mb-4">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <Dropdown
                                    id="payment_method"
                                    v-model="form.payment_method"
                                    :options="paymentMethods"
                                    optionLabel="name"
                                    optionValue="value"
                                    placeholder="Select Payment Method"
                                />
                            </div>
                            <div class="mt-6 flex justify-between items-center">
                                <Button
                                    type="button"
                                    label="Back"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    @click="$inertia.visit(route('pos.index'))"
                                />
                                <Button
                                    type="submit"
                                    label="Confirm Payment"
                                    icon="pi pi-check"
                                    severity="success"
                                    :loading="form.processing"
                                />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </POSLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import POSLayout from '@/Layouts/POSLayout.vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import Button from "primevue/button";
import { format } from 'date-fns';


const props = defineProps({
    order: Object
})

const breadcrumbItems = ref([
    { icon: 'pi pi-shopping-cart', label: 'POS' },
    { label: 'Confirm Purchase' }
]);

const form = useForm({
    payment_method: '',
});

const paymentMethods = ref([
    { name: 'Cash', value: 'cash' },
    { name: 'Card', value: 'card' },
]);

const confirmOrder = () => {
    form.post(route('pos.confirm', props.order.id))
}

const formatDate = (dateString) => {
    return format(new Date(dateString), 'MMMM d, yyyy h:mm a');
};

const { formatCurrency } = useCurrencyFormatter();
</script>
