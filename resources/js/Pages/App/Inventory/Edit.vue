<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Inventory Item</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <InputText id="name" v-model="form.name" :modelValue="props.item.name" class="mt-1 block w-full" required />
                                </div>

                                <div class="col-span-1">
                                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                    <InputText id="sku" v-model="form.sku" :modelValue="props.item.sku" class="mt-1 block w-full" required />
                                </div>

                                <div class="col-span-1">
                                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                    <Dropdown id="category" v-model="form.category_id" :modelValue="props.item.category_id" :options="categories" optionLabel="name" optionValue="id" class="mt-1 block w-full" />
                                </div>

                                <div class="col-span-1">
                                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                    <InputNumber id="price" v-model="form.price" :modelValue="props.item.price" mode="currency" currency="ZAR" locale="en-ZA" class="mt-1 block w-full" />
                                </div>

                                <div class="col-span-1">
                                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                                    <InputNumber id="low_stock_threshold" v-model="form.low_stock_threshold" :modelValue="props.item.low_stock_threshold" class="mt-1 block w-full" />
                                </div>

                                <div class="col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <Textarea id="description" v-model="form.description" :modelValue="props.item.description" rows="3" class="mt-1 block w-full" />
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end space-x-3">
                                <Button type="button" label="Cancel" class="p-button-secondary mr-4" @click="cancel" />
                                <Button
                                    type="submit"
                                    label="Save Changes"
                                    icon="pi pi-save"
                                    :loading="form.processing"
                                    :disabled="!hasChanges || form.processing"
                                    v-tooltip.bottom="!hasChanges ? 'No changes to save' : ''"
                                />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    item: {
        id: number;
        name: string;
        sku: string;
        description: string;
        category_id: number;
        price: number;
        stock: number;
        low_stock_threshold: number;
    };
    categories: Array<{ id: number; name: string }>;
}>();

const originalValues = {
    name: props.item.name,
    sku: props.item.sku,
    description: props.item.description,
    category_id: props.item.category_id,
    price: props.item.price,
    stock: props.item.stock,
    low_stock_threshold: props.item.low_stock_threshold,
};

const form = useForm({
    name: props.item.name,
    sku: props.item.sku,
    description: props.item.description,
    category_id: props.item.category_id,
    price: props.item.price,
    stock: props.item.stock,
    low_stock_threshold: props.item.low_stock_threshold,
});

const submit = () => {
    form.put(route('inventory.update', props.item.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
        },
    });
};

const hasChanges = computed(() => {
    return Object.keys(originalValues).some(key => {
        if (key === 'price') {
            return Number(form[key]).toFixed(2) !== Number(originalValues[key]).toFixed(2);
        }
        return form[key] !== originalValues[key];
    });
});

const cancel = () => {
    window.history.back();
};
</script>
