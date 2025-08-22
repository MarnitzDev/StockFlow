<template>
    <div class="min-h-screen bg-gray-100">
        <Navbar layoutName="POS" />
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header">
                    <h1 class="text-3xl font-bold text-gray-900"></h1>
                </slot>
            </div>
        </header>
        <main class="pt-6">
            <slot />
        </main>
    </div>
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
</script>
