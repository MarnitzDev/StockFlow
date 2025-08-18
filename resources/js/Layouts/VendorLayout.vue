<template>
    <div class="min-h-screen bg-gray-100">
        <Navbar
            layoutName="Vendor"
            :navLinks="navLinks"
            :iconLinks="iconLinks"
        />

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
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const currentRoute = computed(() => usePage().url);

const isActive = (route) => currentRoute.value.startsWith(route);

const navLinks = computed(() => [
    { href: route('home'), label: 'Home', active: route().current('home') },
    { href: route('vendors.index'), label: 'Vendors', active: isActive(route('vendors.index')) },
    { href: route('vendors.purchases.index'), label: 'Purchase History', active: isActive(route('vendors.purchases.index')) },
]);

const iconLinks = [
    { href: route('dashboard'), icon: 'pi-chart-line', text: 'Dashboard' },
    { href: route('pos.index'), icon: 'pi-shopping-cart', text: 'POS' },
];
</script>

