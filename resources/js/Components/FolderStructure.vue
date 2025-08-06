<template>
    <div class="bg-white shadow rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">All Items</h3>
<!--            <Button label="ADD NEW" icon="pi pi-plus" class="p-button-primary" />-->
            <Link :href="route('products.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Product
            </Link>
        </div>
        <div class="flex flex-wrap gap-2 mb-6">
            <Button type="button" icon="pi pi-plus" label="Expand All" @click="expandAll" />
            <Button type="button" icon="pi pi-minus" label="Collapse All" @click="collapseAll" />
        </div>
        <Tree v-model:expandedKeys="expandedKeys" :value="nodes" selectionMode="single" v-model:selectionKeys="selectedKey">
            <template #default="slotProps">
                <span class="flex ">
                    <i :class="slotProps.node.icon" class=""></i>
                    {{ slotProps.node.label }}
                </span>
            </template>
        </Tree>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';


const nodes = ref([
    {
        key: '1',
        label: 'All Items',
        icon: 'pi pi-folder',
        children: [
            {
                key: '2',
                label: 'Storage Room',
                icon: 'pi pi-inbox',
            },
            {
                key: '3',
                label: 'Maintenance Cabinet',
                icon: 'pi pi-cog',
            }
        ]
    }
]);

const selectedKey = ref(null);
const expandedKeys = ref({});

const expandAll = () => {
    for (let node of nodes.value) {
        expandNode(node);
    }
};

const collapseAll = () => {
    expandedKeys.value = {};
};

const expandNode = (node) => {
    if (node.children && node.children.length) {
        expandedKeys.value[node.key] = true;
        for (let child of node.children) {
            expandNode(child);
        }
    }
};
</script>

<style scoped>
</style>
