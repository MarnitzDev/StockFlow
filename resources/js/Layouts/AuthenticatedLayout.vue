<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-xl z-10 transition-all duration-300 ease-in-out flex flex-col">
            <div class="flex items-center justify-center h-20 border-b border-gray-200">
                <ApplicationLogo class="w-12 h-12" />
            </div>
            <nav class="mt-5 flex-grow">
                <div v-for="item in menuItems" :key="item.label" class="px-2 py-2">
                    <Link v-if="!item.submenu" :href="route(item.route)" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                        <i :class="[item.icon, 'mr-3 text-gray-400']"></i>
                        {{ item.label }}
                    </Link>
                    <div v-else>
                        <button @click="toggleSubmenu(item)" class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none rounded-md">
                            <i :class="[item.icon, 'mr-3 text-gray-400']"></i>
                            {{ item.label }}
                            <svg class="ml-auto h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="item.isOpen" class="mt-2 space-y-2 px-4">
                            <Link v-for="subItem in item.submenu" :key="subItem.label" :href="route(subItem.route)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                                {{ subItem.label }}
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow h-20">
                <div class="py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="flex-1 flex items-center">
                            <IconField class="w-full max-w-3xl mr-4">
                                <InputIcon class="pi pi-search" />
                                <InputText v-model="globalSearch" placeholder="Search" class="w-full" />
                            </IconField>
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight whitespace-nowrap">
                                <slot name="header" />
                            </h2>
                        </div>
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                        {{ $page.props.auth.user.name }}
                                        <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <!-- Summary section -->
                <div class="py-6 bg-gray-100 border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <slot name="summary"></slot>
                    </div>
                </div>
                <div class="mx-auto px-6 py-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const menuItems = ref([
    { label: 'Dashboard', icon: 'fas fa-tachometer-alt', route: 'dashboard' },
    {
        label: 'Inventory',
        icon: 'fas fa-box',
        submenu: [
            { label: 'Items', route: 'inventory.items' },
            { label: 'Categories', route: 'inventory.categories.index' },
            { label: 'Stock Adjustments', route: 'inventory.stockAdjustments' },
            // { label: 'Stock History', route: 'inventory.stockHistory' },
        ]
    },
    {
        label: 'Sales',
        icon: 'fas fa-shopping-cart',
        submenu: [
            { label: 'Orders', route: 'sales.orders.index' },
            { label: 'Create Order', route: 'sales.orders.create' },
            // Add more sales-related submenu items as needed
        ]
    },
    {
        label: 'Customers',
        icon: 'fas fa-address-book',
        submenu: [
            { label: 'List All', route: 'contacts.customers.index' },
            { label: 'Add New', route: 'contacts.customers.create' },
        ]
    },
    {
        label: 'Suppliers',
        icon: 'fas fa-truck',
        submenu: [
            // { label: 'List All', route: 'contacts.suppliers.index' },
            // { label: 'Add New', route: 'contacts.suppliers.create' },
        ]
    },
    // Uncomment and add routes as needed
    // { label: 'Contacts', icon: 'fas fa-address-book', route: 'contacts.index' },
    // { label: 'Reports', icon: 'fas fa-chart-bar', route: 'inventory.report' },
]);

const toggleSubmenu = (item) => {
    item.isOpen = !item.isOpen;
};
</script>

<style scoped>
/* Override PrimeVue Menu styles to fit your design */
:deep(.p-menu) {
    border: none;
    width: 100%;
    background-color: transparent;
}

:deep(.p-menu .p-menuitem-link) {
    padding: 0.75rem 1rem;
}

:deep(.p-menu .p-menuitem-icon) {
    color: #4b5563;
}

:deep(.p-menu .p-menuitem-text) {
    color: #374151;
}

:deep(.p-menu .p-menuitem-link:focus) {
    box-shadow: none;
}

:deep(.p-menu .p-menuitem-link:hover) {
    background-color: #f3f4f6;
}

:deep(.p-menu .p-menuitem-link.router-link-active) {
    background-color: #e5e7eb;
}

:deep(.p-menu .p-submenu-header) {
    background-color: transparent;
    margin: 0;
    padding: 0.75rem 1rem;
    font-weight: 600;
    color: #1f2937;
}
</style>
