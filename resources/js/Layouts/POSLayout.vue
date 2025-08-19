<template>
    <div class="min-h-screen bg-gray-100">
        <Navbar
            layoutName="POS"
            :navLinks="navLinks"
            :iconLinks="iconLinks"
        />

        <!-- New header slot for page title and description -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header">
                    <!-- Default content if no header is provided -->
                    <h1 class="text-3xl font-bold text-gray-900">
                        Page Title
                    </h1>
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
import { computed } from 'vue';

const navLinks = computed(() => [
    { href: route('home'), label: 'Home', active: route().current('home') },
    { href: route('pos.index'), label: 'Purchase Items', active: route().current('pos.index') },
    // { href: route('pos.history'), label: 'Purchase History', active: route().current('pos.history') },
]);

const iconLinks = [
    { href: route('dashboard'), icon: 'pi-chart-line', text: 'Dashboard' },
    { href: route('vendors.index'), icon: 'pi-truck', text: 'Vendors' },
];
</script>
