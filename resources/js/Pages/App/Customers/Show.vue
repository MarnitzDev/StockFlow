<template>
    <Head title="Customer Details" />
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ customer.name }}</h3>
                            <p class="mt-1 text-sm text-gray-600">Customer ID: {{ customer.id }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase">Contact Information</h4>
                                <p class="mt-2"><strong>Email:</strong> {{ customer.email }}</p>
                                <p class="mt-1"><strong>Phone:</strong> {{ customer.phone }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase">Address</h4>
                                <p class="mt-2">{{ customer.address }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <Link
                                :href="route('contacts.customers.edit', customer.id)"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            >
                                Edit Customer
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

interface Customer {
    id: number;
    name: string;
    email: string;
    phone: string;
    address: string;
    // Add any other customer properties here
}

interface Props {
    customer: Customer;
}

const props = defineProps<Props>();
</script>
