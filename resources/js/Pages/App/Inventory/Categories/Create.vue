<template>
    <Head title="Category Create" />
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <h2 class="text-2xl font-bold mb-6">Create New Category</h2>
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- First Column -->
                    <div class="overflow-hidden sm:rounded-lg md:w-2/5">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-xl font-semibold mb-4">Basic Information</h3>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <InputLabel for="name" value="Name" />
                                    <InputText
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        @input="generateSlug"
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="slug" value="Slug" />
                                    <InputText
                                        id="slug"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.slug"
                                        required
                                    />
                                    <InputError :message="form.errors.slug" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <InputLabel for="description" value="Description" />
                                <Textarea
                                    id="description"
                                    class="mt-1 w-full"
                                    v-model="form.description"
                                    rows="5"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Second Column -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg md:w-3/5">
                        <div class="p-6 bg-white">
                            <h3 class="text-xl font-semibold mb-4">Category Details</h3>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <InputLabel for="parent_category" value="Parent Category" />
                                    <Dropdown
                                        id="parent_category"
                                        v-model="form.parent_id"
                                        :options="categories"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Select a parent category"
                                        class="w-full mt-1"
                                        :disabled="isParentCategory"
                                    />
                                    <InputError :message="form.errors.parent_id" class="mt-2" />
                                </div>
                                <div class="flex items-center">
                                    <div class="flex items-center mt-6 ml-4">
                                        <Checkbox
                                            id="is_parent_category"
                                            v-model="isParentCategory"
                                            :binary="true"
                                        />
                                        <InputLabel for="is_parent_category" value="Is Parent Category" class="ml-2" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="items" value="Items" />
                                <MultiSelect
                                    id="items"
                                    v-model="form.item_ids"
                                    :options="items"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Select items"
                                    class="w-full mt-1"
                                    :maxSelectedLabels="2"
                                    :showToggleAll="false"
                                    :filter="true"
                                    showClear
                                />
                                <InputError :message="form.errors.item_ids" class="mt-2" />

                                <!-- Display selected items -->
                                <div v-if="form.item_ids.length > 0" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Items:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <Chip
                                            v-for="itemId in form.item_ids"
                                            :key="itemId"
                                            :label="getItemName(itemId)"
                                            class="bg-blue-100 text-blue-800"
                                            removable
                                            @remove="removeItem(itemId)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-between">
                    <Link :href="route('inventory.categories.index')" class="mr-4">
                        <Button
                            label="Back to Categories"
                            icon="pi pi-arrow-left"
                            severity="secondary"
                        />
                    </Link>
                    <Button
                        label="Create Category"
                        icon="pi pi-plus"
                        @click="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    categories: Array,
    items: Array,
});

const form = useForm({
    name: '',
    slug: '',
    description: '',
    parent_id: null,
    item_ids: [],
});

const isParentCategory = ref(false);

watch(isParentCategory, (newValue) => {
    if (newValue) {
        form.parent_id = null;
    }
});

const submit = () => {
    form.post(route('inventory.categories.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const generateSlug = () => {
    form.slug = form.name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
};

const getItemName = (itemId) => {
    const item = props.items.find(item => item.id === itemId);
    return item ? item.name : '';
};

const removeItem = (itemId) => {
    form.item_ids = form.item_ids.filter(id => id !== itemId);
};

const selectedItemsCount = computed(() => form.item_ids.length);
</script>
