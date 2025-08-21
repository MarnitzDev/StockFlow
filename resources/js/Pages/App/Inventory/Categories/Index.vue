<template>
    <AuthenticatedLayout>
        <template #header-actions>
            <Link :href="route('inventory.categories.create')">
                <Button
                    label="Add New Category"
                    icon="pi pi-plus"
                    variant="text"
                />
            </Link>
        </template>
        <div class="pb-12">
            <div class="px-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <TreeTable
                        :value="treeTableData"
                        :expandedKeys="expandedKeys"
                        @toggle="onToggle"
                        :filters="filters"
                        filterMode="lenient"
                        dataKey="id"
                    >
                        <template #header>
                            <div class="flex flex-wrap gap-2 items-center justify-between">
                                <h4 class="text-xl font-bold">Inventory Categories</h4>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="filters['global']" placeholder="Search..." />
                                </IconField>
                            </div>
                        </template>
                        <Column field="name" header="Category Name" expander>
                            <template #body="{ node }">
                                {{ node.data.name }}
                            </template>
                        </Column>
                        <Column field="slug" header="Slug">
                            <template #body="{ node }">
                                {{ node.data.slug }}
                            </template>
                        </Column>
                        <Column field="totalItems" header="Total Items">
                            <template #body="{ node }">
                                {{ node.data.totalItems }}
                            </template>
                        </Column>
                        <Column field="totalStock" header="Total Stock">
                            <template #body="{ node }">
                                {{ node.data.totalStock }}
                            </template>
                        </Column>
                        <Column header="Actions">
                            <template #body="{ node }">
                                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editCategory(node.data)" />
                            </template>
                        </Column>
                    </TreeTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

const props = defineProps({
    categories: Array
});

const filters = ref({});
const expandedKeys = ref({});

const treeTableData = computed(() => {
    return props.categories.filter(category => !category.parent_id).map(category => ({
        key: category.id,
        data: {
            ...category,
            totalItems: getCategoryItemCount(category) + getChildrenItemCount(category),
            totalStock: getCategoryStock(category) + getChildrenStock(category)
        },
        children: getChildrenNodes(category.id)
    }));
});

const getChildrenNodes = (parentId) => {
    return props.categories
        .filter(category => category.parent_id === parentId)
        .map(child => ({
            key: child.id,
            data: {
                ...child,
                totalItems: getCategoryItemCount(child) + getChildrenItemCount(child),
                totalStock: getCategoryStock(child) + getChildrenStock(child)
            },
            children: getChildrenNodes(child.id)
        }));
};

const getCategoryStock = (category) => {
    return category.inventory_items?.reduce((total, item) => total + item.stock, 0) || 0;
};

const getCategoryItemCount = (category) => {
    return category.inventory_items?.length || 0;
};

const getChildrenItemCount = (category) => {
    const children = props.categories.filter(c => c.parent_id === category.id);
    return children.reduce((total, child) => total + getCategoryItemCount(child) + getChildrenItemCount(child), 0);
};

const getChildrenStock = (category) => {
    const children = props.categories.filter(c => c.parent_id === category.id);
    return children.reduce((total, child) => total + getCategoryStock(child) + getChildrenStock(child), 0);
};

const onToggle = (event) => {
    expandedKeys.value = event;
};

const editCategory = (category) => {
    router.visit(route('inventory.categories.edit', category.id));
};
</script>
