<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Organization - Locations" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Organization - Locations</h2>
        </template>
        <div class="py-12">
            Organization - Locations
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
