<template>
    <AuthenticatedLayout>
        <div class="pb-12">
            <div class="px-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Editing Category: {{ category.name }}
                    </h2>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white flex flex-col">
                        <div class="flex">
                            <!-- Vertical Tabs -->
                            <div class="w-1/4 pr-4 border-r">
                                <div
                                    v-for="(tab, index) in tabs"
                                    :key="index"
                                    @click="activeTab = index"
                                    class="py-2 px-4 cursor-pointer mb-2 rounded transition-colors duration-200 ease-in-out"
                                    :class="activeTab === index ? 'bg-blue-500 text-white' : 'hover:bg-gray-100'"
                                >
                                    {{ tab }}
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="w-3/4 pl-6">
                                <!-- Category Details Tab -->
                                <div v-if="activeTab === 0">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <h3 class="text-lg font-semibold mb-4">Category Details</h3>
                                            <form @submit.prevent="submit">
                                                <div class="mb-4">
                                                    <InputLabel for="name" value="Name" />
                                                    <TextInput
                                                        id="name"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.name"
                                                        required
                                                        @input="generateSlug"
                                                    />
                                                    <InputError :message="form.errors.name" class="mt-2" />
                                                </div>

                                                <div class="mb-4">
                                                    <InputLabel for="slug" value="Slug" />
                                                    <TextInput
                                                        id="slug"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        v-model="form.slug"
                                                        required
                                                    />
                                                    <InputError :message="form.errors.slug" class="mt-2" />
                                                </div>

                                                <div class="mb-4">
                                                    <InputLabel for="description" value="Description" />
                                                    <Textarea
                                                        id="description"
                                                        class="mt-1 block w-full"
                                                        v-model="form.description"
                                                        rows="5"
                                                    />
                                                    <InputError :message="form.errors.description" class="mt-2" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Hierarchy Tab -->
                                <div v-if="activeTab === 1">
                                    <h3 class="text-lg font-semibold mb-4">Category Hierarchy</h3>
                                    <div class="overflow-x-auto">
                                        <OrganizationChart :value="categoryHierarchy" class="w-full min-w-max">
                                            <template #default="slotProps">
                                                <div class="flex flex-col items-center p-2 rounded-lg"
                                                     :class="{
                                                        'bg-blue-500 text-white': slotProps.node.key === props.category.id.toString()
                                                    }">
                                                    <span class="font-medium">{{ slotProps.node.label }}</span>
                                                </div>
                                            </template>
                                        </OrganizationChart>
                                    </div>
                                </div>

                                <!-- Category Items Tab -->
                                <div v-if="activeTab === 2">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold">Category Items</h3>
                                        <Button label="Add Items" icon="pi pi-plus" @click="openAddItemsDialog" />
                                    </div>
                                    <DataTable :value="categoryItems" :paginator="true" :rows="5">
                                        <Column field="name" header="Name"></Column>
                                        <Column field="sku" header="SKU"></Column>
                                        <Column field="stock" header="Stock"></Column>
                                        <Column header="Actions">
                                            <template #body="slotProps">
                                                <Button icon="pi pi-trash" class="p-button-danger p-button-sm" @click="removeItemFromCategory(slotProps.data)" />
                                            </template>
                                        </Column>
                                    </DataTable>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Save Button outside tabs -->
                <div class="mt-6 flex justify-end">
                    <PrimaryButton @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save Changes
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="showAddItemsDialog" modal header="Add Items to Category" :style="{width: '30vw'}">
            <MultiSelect
                v-model="selectedItemsToAdd"
                :options="availableItems"
                optionLabel="name"
                placeholder="Select Items"
                class="w-full mb-4"
                :maxSelectedLabels="2"
                :showToggleAll="false"
                :filter="true"
                showClear
            />
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="showAddItemsDialog = false" class="p-button-text"/>
                <Button label="Add" icon="pi pi-check" @click="addSelectedItemsToCategory" autofocus />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    allItems: {
        type: Array,
        required: true,
    },
    allCategories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: props.category.name,
    slug: props.category.slug,
    description: props.category.description,
    parent_id: props.category.parent_id,
});

const categoryItems = ref(props.category.inventory_items || []);
const showAddItemsDialog = ref(false);
const selectedItemsToAdd = ref([]);

const tabs = ['Category Details', 'Category Hierarchy', 'Category Items'];
const activeTab = ref(0);

const availableItems = computed(() => {
    return props.allItems.filter(item => !categoryItems.value.some(catItem => catItem.id === item.id));
});

const availableParentCategories = computed(() => {
    return props.allCategories ? props.allCategories.filter(cat => cat.id !== props.category.id) : [];
});

const childCategories = computed(() => {
    return props.allCategories ? props.allCategories.filter(cat => cat.parent_id === props.category.id) : [];
});

const getParentCategoryName = (parentId) => {
    if (!props.allCategories) return 'Unknown';
    const parent = props.allCategories.find(cat => cat.id === parentId);
    return parent ? parent.name : 'Unknown';
};

const categoryHierarchy = computed(() => {
    const buildHierarchy = (category, isRoot = false) => {
        const node = {
            key: category.id.toString(),
            type: 'category',
            label: category.name,
            expanded: isRoot,
            children: []
        };

        const children = props.allCategories.filter(cat => cat.parent_id === category.id);
        node.children = children.map(child => buildHierarchy(child));

        return node;
    };

    if (form.parent_id) {
        const parent = props.allCategories.find(cat => cat.id === form.parent_id);
        return buildHierarchy(parent, true);
    } else {
        return buildHierarchy(props.category, true);
    }
});

const submit = () => {
    form.put(route('inventory.categories.update', props.category.id), {
        preserveScroll: true,
    });
};

const generateSlug = () => {
    if (!props.category.slug) {
        form.slug = form.name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    }
};

const removeItemFromCategory = (item) => {
    categoryItems.value = categoryItems.value.filter(catItem => catItem.id !== item.id);
    // You might want to make an API call here to update the backend
};

const openAddItemsDialog = () => {
    showAddItemsDialog.value = true;
};

const addSelectedItemsToCategory = () => {
    categoryItems.value = [...categoryItems.value, ...selectedItemsToAdd.value];
    selectedItemsToAdd.value = [];
    showAddItemsDialog.value = false;
    // You might want to make an API call here to update the backend
};
</script>

<style scoped>
.p-organizationchart .p-organizationchart-node-content {
    padding: 0.5rem;
    border-radius: 4px;
}
.p-organizationchart .p-organizationchart-node-content.category {
    font-weight: bold;
}
</style>
