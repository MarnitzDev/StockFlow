<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-blue-700 border-b border-blue-600 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <!-- You can add a logo here if needed -->
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('home')" :active="route().current('home')" class="text-white">
                                Home
                            </NavLink>
                            <NavLink :href="route('pos.index')" :active="route().current('pos.index')" class="text-white">
                                POS
                            </NavLink>
                            <NavLink :href="route('pos.history')" :active="route().current('pos.history')" class="text-white">
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

        <main class="pt-6"> <!-- Reduced padding-top since we now have a header -->
            <slot />
        </main>
    </div>
</template>

<script setup>
import NavLink from '@/Components/NavLink.vue';
</script>

<style scoped>
.nav-link {
    @apply inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out;
}

.nav-link:not(.active) {
    @apply border-transparent text-blue-100 hover:text-white hover:border-blue-300;
}

.nav-link.active {
    @apply border-white text-white;
}
</style>
