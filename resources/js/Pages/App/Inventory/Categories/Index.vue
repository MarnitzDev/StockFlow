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
                <!--                -->
                <!--                <div class="pb-6 grid grid-cols-1 md:grid-cols-3 gap-4">-->
                <!--                    <div class="bg-white overflow-hidden shadow rounded-lg">-->
                <!--                        <div class="px-4 py-3 sm:p-4">-->
                <!--                            <dt class="text-xs font-medium text-gray-500 truncate">-->
                <!--                                Total Categories-->
                <!--                            </dt>-->
                <!--                            <dd class="mt-1 text-2xl font-semibold text-gray-900">-->
                <!--                                {{ categories.length }}-->
                <!--                            </dd>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="bg-white overflow-hidden shadow rounded-lg">-->
                <!--                        <div class="px-4 py-3 sm:p-4">-->
                <!--                            <dt class="text-xs font-medium text-gray-500 truncate">-->
                <!--                                Total Items in Categories-->
                <!--                            </dt>-->
                <!--                            <dd class="mt-1 text-2xl font-semibold text-gray-900">-->
                <!--                                {{ calculateTotalItems }}-->
                <!--                            </dd>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="bg-white overflow-hidden shadow rounded-lg">-->
                <!--                        <div class="px-4 py-3 sm:p-4">-->
                <!--                            <dt class="text-xs font-medium text-gray-500 truncate">-->
                <!--                                Total Stock in Categories-->
                <!--                            </dt>-->
                <!--                            <dd class="mt-1 text-2xl font-semibold text-gray-900">-->
                <!--                                {{ calculateTotalStock }}-->
                <!--                            </dd>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <TreeTable
                        :value="treeTableData"
                        :expandedKeys="expandedKeys"
                        :filters="filters"
                        @toggle="expandedKeys = $event"
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
                        <Column field="name" header="Category Name" style="width: 30%;" expander>
                            <template #body="{ node }">
                                {{ node.data.name }}
                            </template>
                        </Column>
                        <Column field="slug" header="Slug" style="width: 30%;">
                            <template #body="{ node }">
                                {{ node.data.slug }}
                            </template>
                        </Column>
                        <Column field="totalItems" header="Total Items" style="width: 15%;">
                            <template #body="{ node }">
                                {{ node.data.totalItems }}
                            </template>
                        </Column>
                        <Column field="totalStock" header="Total Stock" style="width: 15%;">
                            <template #body="{ node }">
                                {{ node.data.totalStock }}
                            </template>
                        </Column>
                        <Column header="Actions" style="width: 10%;">
                            <template #body="{ node }">
                                <!--                                <Link :href="route('inventory.categories.edit', node.data.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</Link>-->
                                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editCategory(node.data)" />
                                <!-- Add delete functionality here -->

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

const props = defineProps({
    categories: Array
});

const expandedKeys = ref({});
const filters = ref({});

const treeTableData = computed(() => {
    return props.categories.filter(category => !category.parent_id).map(category => ({
        key: category.id,
        data: {
            ...category,
            totalItems: getTotalItems(category),
            totalStock: getTotalStock(category)
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
                totalItems: getTotalItems(child),
                totalStock: getTotalStock(child)
            },
            children: getChildrenNodes(child.id)
        }));
};

const getTotalItems = (category) => {
    const directItems = getCategoryItemCount(category);
    const childrenItems = getChildrenItemCount(category);
    return directItems + childrenItems;
};

const getTotalStock = (category) => {
    const directStock = getCategoryStock(category);
    const childrenStock = getChildrenStock(category);
    return directStock + childrenStock;
};

const getCategoryStock = (category) => {
    return category.inventory_items?.reduce((total, item) => total + item.stock, 0) || 0;
};

const getCategoryItemCount = (category) => {
    return category.inventory_items?.length || 0;
};

const getChildrenItemCount = (category) => {
    const children = props.categories.filter(c => c.parent_id === category.id);
    return children.reduce((total, child) => total + getTotalItems(child), 0);
};

const getChildrenStock = (category) => {
    const children = props.categories.filter(c => c.parent_id === category.id);
    return children.reduce((total, child) => total + getTotalStock(child), 0);
};

const editCategory = (category) => {
    router.visit(route('inventory.categories.edit', category.id));
};
</script>
