<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';


const currentRoute = computed(() => usePage().url);

const isActive = (route: string) => {
    return currentRoute.value.startsWith(route);
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <!-- You can add a logo here if needed -->
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('home')" :active="isActive(route('home'))">
                                Home
                            </NavLink>
                            <NavLink :href="route('vendors.index')" :active="isActive(route('vendors.index'))">
                                Vendors
                            </NavLink>
                            <NavLink :href="route('vendors.purchases.index')" :active="isActive(route('vendors.purchases.index'))">
                                Purchase History
                            </NavLink>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Add any additional navigation items or user menu here -->
                    </div>
                </div>
            </div>
        </nav>

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
