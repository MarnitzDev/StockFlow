<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import FileUpload from 'primevue/fileupload';
import Button from 'primevue/button';
import { Head } from '@inertiajs/vue3';

const form = useForm({
    companyName: '',
    tradingName: '',
    registrationNumber: '',
    vatNumber: '',
    website: '',
    primaryContact: {
        name: '',
        email: '',
        phone: '',
    },
    billingAddress: {
        street: '',
        city: '',
        state: '',
        postalCode: '',
        country: '',
    },
    shippingAddress: {
        sameAsBilling: true,
        street: '',
        city: '',
        state: '',
        postalCode: '',
        country: '',
    },
    paymentTerms: '',
    creditLimit: 0,
    supplierType: '',
    documents: [],
});

const supplierTypes = ref(['Manufacturer', 'Wholesaler', 'Distributor', 'Importer']);

const submitForm = () => {
    form.post(route('suppliers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success (e.g., show notification, redirect)
        },
    });
};

const onFileUpload = (event) => {
    form.documents = [...form.documents, ...event.files];
};
</script>

<template>
    <Head title="Add Supplier" />

    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submitForm">
                            <!-- Company Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Company Information</h3>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="companyName" class="block text-sm font-medium text-gray-700">Company Name</label>
                                        <InputText id="companyName" v-model="form.companyName" type="text" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <label for="tradingName" class="block text-sm font-medium text-gray-700">Trading Name</label>
                                        <InputText id="tradingName" v-model="form.tradingName" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <label for="registrationNumber" class="block text-sm font-medium text-gray-700">Registration Number</label>
                                        <InputText id="registrationNumber" v-model="form.registrationNumber" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <label for="vatNumber" class="block text-sm font-medium text-gray-700">VAT Number</label>
                                        <InputText id="vatNumber" v-model="form.vatNumber" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                        <InputText id="website" v-model="form.website" type="url" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <label for="supplierType" class="block text-sm font-medium text-gray-700">Supplier Type</label>
                                        <Dropdown id="supplierType" v-model="form.supplierType" :options="supplierTypes" class="mt-1 block w-full" />
                                    </div>
                                </div>
                            </div>

                            <!-- Primary Contact -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Primary Contact</h3>
                                <div class="grid grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <label for="contactName" class="block text-sm font-medium text-gray-700">Name</label>
                                        <InputText id="contactName" v-model="form.primaryContact.name" type="text" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <label for="contactEmail" class="block text-sm font-medium text-gray-700">Email</label>
                                        <InputText id="contactEmail" v-model="form.primaryContact.email" type="email" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <label for="contactPhone" class="block text-sm font-medium text-gray-700">Phone</label>
                                        <InputText id="contactPhone" v-model="form.primaryContact.phone" type="tel" class="mt-1 block w-full" />
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Address -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Billing Address</h3>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="col-span-2">
                                        <label for="billingStreet" class="block text-sm font-medium text-gray-700">Street</label>
                                        <InputText id="billingStreet" v-model="form.billingAddress.street" type="text" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <label for="billingCity" class="block text-sm font-medium text-gray-700">City</label>
                                        <InputText id="billingCity" v-model="form.billingAddress.city" type="text" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <label for="billingState" class="block text-sm font-medium text-gray-700">State/Province</label>
                                        <InputText id="billingState" v-model="form.billingAddress.state" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <label for="billingPostalCode" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                        <InputText id="billingPostalCode" v-model="form.billingAddress.postalCode" type="text" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <label for="billingCountry" class="block text-sm font-medium text-gray-700">Country</label>
                                        <InputText id="billingCountry" v-model="form.billingAddress.country" type="text" class="mt-1 block w-full" required />
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Financial Information</h3>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="paymentTerms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                                        <InputText id="paymentTerms" v-model="form.paymentTerms" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <label for="creditLimit" class="block text-sm font-medium text-gray-700">Credit Limit</label>
                                        <InputNumber id="creditLimit" v-model="form.creditLimit" mode="currency" currency="ZAR" locale="en-ZA" class="mt-1 block w-full" />
                                    </div>
                                </div>
                            </div>

                            <!-- Document Upload -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Documents</h3>
                                <FileUpload mode="basic" name="documents[]" url="/api/upload" @upload="onFileUpload" :multiple="true" accept="image/*,application/pdf" :maxFileSize="5000000" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Button type="submit" label="Add Supplier" icon="pi pi-check" class="p-button-primary" :loading="form.processing" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
