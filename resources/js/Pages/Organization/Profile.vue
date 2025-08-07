<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Organization - Profile" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Organization - Profile</h2>
        </template>
        <div class="py-12">
            Organization - Profile
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
