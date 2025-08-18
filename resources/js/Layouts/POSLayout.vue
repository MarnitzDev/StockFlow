<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-blue-700 border-b border-blue-600 sticky top-0 z-50">
            <div class="px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')" class="flex items-center">
                                <span class="font-['Outfit'] text-white text-xl font-bold mr-2">StockFlow</span>
                            </Link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('pos.index')" :active="route().current('pos.index')" class="text-white">
                                Purchase Items
                            </NavLink>
                            <NavLink :href="route('pos.history')" :active="route().current('pos.history')" class="text-white">
                                Purchase History
                            </NavLink>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <Select v-model="selectedSection" :options="sections" optionLabel="name"
                                @change="changeSection">
                            <template #value="slotProps">
                                <div class="flex items-center">
                                    <span>{{ slotProps.value.name }}</span>
                                </div>
                            </template>
                            <template #option="slotProps">
                                <div class="flex items-center">
                                    <span>{{ slotProps.option.name }}</span>
                                </div>
                            </template>
                        </Select>
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
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const sections = ref([
    { name: 'POS', route: 'pos.index' },
    { name: 'Dashboard', route: 'dashboard' },
    { name: 'Vendors', route: 'vendors.index' }
]);

const selectedSection = ref(sections.value[0]);

const changeSection = () => {
    navigateTo(route(selectedSection.value.route));
};

const navigateTo = (routePath) => {
    window.location = routePath;
};
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
