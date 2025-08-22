<template>
    <Head title="Inventory Edit" />
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
                                <div class="col-span-2">
                                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                                    <div class="flex items-center space-x-6">
                                        <div class="w-32 h-32 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                            <img
                                                v-if="imagePreview"
                                                :src="imagePreview"
                                                alt="Product image preview"
                                                class="w-full h-full object-cover"
                                            />
                                            <img
                                                v-else-if="props.item.image_url"
                                                :src="props.item.image_url"
                                                alt="Current product image"
                                                class="w-full h-full object-cover"
                                            />
                                            <span v-else class="text-gray-400">No image</span>
                                        </div>
                                        <div>
                                            <FileUpload
                                                mode="basic"
                                                name="image"
                                                accept="image/*"
                                                :maxFileSize="1000000"
                                                @select="onImageSelect"
                                                @uploader="onImageUpload"
                                                :auto="true"
                                                chooseLabel="Choose Image"
                                                class="p-button-outlined p-button-primary"
                                            />
                                            <p class="mt-2 text-sm text-gray-500">
                                                PNG, JPG, GIF up to 1MB
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <InputText id="name" v-model="form.name" :modelValue="props.item.name" class="mt-1 block w-full" required />
                                </div>

                                <div class="col-span-1">
                                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                    <InputText
                                        id="sku"
                                        :modelValue="props.item.sku"
                                        class="mt-1 block w-full bg-gray-100"
                                        disabled
                                    />
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

                            <div class="mt-6 flex items-center justify-between space-x-3">
                                <Link :href="route('inventory.items')" class="mr-4">
                                    <Button
                                        label="Back to Inventory"
                                        icon="pi pi-arrow-left"
                                        severity="secondary"
                                    />
                                </Link>
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
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
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
        image_url: string | null;
    };
    categories: Array<{ id: number; name: string }>;
}>();

const originalValues = {
    name: props.item.name,
    description: props.item.description,
    category_id: props.item.category_id,
    price: props.item.price,
    stock: props.item.stock,
    low_stock_threshold: props.item.low_stock_threshold,
    image_url: props.item.image_url,
};

const imagePreview = ref(null);

const onImageSelect = (event) => {
    const file = event.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const onImageUpload = (event) => {
    const file = event.files[0];
    form.image = file;
};

const form = useForm({
    name: props.item.name,
    description: props.item.description,
    category_id: props.item.category_id,
    price: props.item.price,
    stock: props.item.stock,
    low_stock_threshold: props.item.low_stock_threshold,
    image: null,
});

const submit = () => {
    form.put(route('inventory.update', props.item.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
            imagePreview.value = null;
        },
    });
};

const hasChanges = computed(() => {
    return Object.keys(originalValues).some(key => {
        if (key === 'price') {
            return Number(form[key]).toFixed(2) !== Number(originalValues[key]).toFixed(2);
        }
        if (key === 'image_url') {
            return form.image !== null || imagePreview.value !== null;
        }
        return form[key] !== originalValues[key];
    });
});
</script>
