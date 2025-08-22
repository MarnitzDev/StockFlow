<template>
    <div class="min-h-screen bg-gray-100">
        <Navbar layoutName="Vendor" />
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header"></slot>
            </div>
        </header>
        <main class="py-12">
            <div class="px-6">
                <slot></slot>
            </div>
        </main>
    </div>
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
</script>

