<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
<!--                            <Link :href="route('dashboard')">-->
<!--&lt;!&ndash;                                <ApplicationLogo class="block h-9 w-auto" />&ndash;&gt;-->
<!--                            </Link>-->
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('home')" :active="route().current('home')">
                                Home
                            </NavLink>
                            <NavLink :href="route('pos.index')" :active="route().current('pos.index')">
                                POS
                            </NavLink>
                            <NavLink :href="route('pos.history')" :active="route().current('pos.history')">
                                Order History
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
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import NavLink from '@/Components/NavLink.vue';
</script>
