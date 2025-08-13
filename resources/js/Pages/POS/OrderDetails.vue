<template>
    <POSLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Order #{{ order.order_number }}</h3>

                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Customer Information</h4>
                            <p>Name: {{ order.customer.name }}</p>
                            <p>Email: {{ order.customer.email }}</p>
                        </div>

                        <div class="mb-6">
                            <h4 class="font-medium mb-2">Order Items</h4>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in order.items" :key="item.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ item.inventory.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(item.unit_price) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(item.subtotal) }}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">Total:</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ formatCurrency(order.total_amount) }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        <form @submit.prevent="confirmOrder" class="mt-6">
                            <div class="mb-4">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select id="payment_method" v-model="form.payment_method" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Confirm Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </POSLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import POSLayout from '@/Layouts/POSLayout.vue';

const props = defineProps({
    order: Object
})

const form = useForm({
    payment_method: 'cash',
})

const confirmOrder = () => {
    form.post(route('pos.confirm', props.order.id))
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount)
}
</script>
