<template>
    <div class="bg-white shadow rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">All Items</h3>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                ADD NEW
            </button>
        </div>
        <ul class="space-y-2">
            <li v-for="folder in folders" :key="folder.id" class="flex items-center">
                <i :class="folder.icon" class="mr-2"></i>
                {{ folder.name }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const folders = ref([
    { id: 1, name: 'All Items', icon: 'fas fa-folder' },
    { id: 2, name: 'Storage Room', icon: 'fas fa-box' },
    { id: 3, name: 'Maintenance Cabinet', icon: 'fas fa-tools' },
]);
</script>
