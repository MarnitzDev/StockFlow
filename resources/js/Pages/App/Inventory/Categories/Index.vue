<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const { formatCurrency } = useCurrencyFormatter();

const props = defineProps({
    categories: Array
});

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
                totalItems: getCategoryItemCount(child),
                totalStock: getCategoryStock(child)
            }
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
    return children.reduce((total, child) => total + getCategoryItemCount(child), 0);
};

const getChildrenStock = (category) => {
    const children = props.categories.filter(c => c.parent_id === category.id);
    return children.reduce((total, child) => total + getCategoryStock(child), 0);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Inventory Categories</h3>
                        <Link :href="route('inventory.categories.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Category
                        </Link>
                    </div>
                    <TreeTable :value="treeTableData" :expandedKeys="expandedKeys" @toggle="expandedKeys = $event">
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
                                <Link :href="route('inventory.categories.edit', node.data.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</Link>
                                <!-- Add delete functionality here -->
                            </template>
                        </Column>
                    </TreeTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
