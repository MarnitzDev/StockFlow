<template>
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Fixed Header -->
        <header class="bg-blue-700 border-b border-blue-600 fixed top-0 left-0 right-0 z-50">
            <div class="px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <div class="flex items-center">
                                <Link :href="route('home')" class="flex items-center">
                                    <span class="font-['Outfit'] text-white text-xl font-bold mr-1">StockFlow</span>
                                </Link>
                            </div>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink v-for="link in navLinks" :key="link.href" :href="link.href" :active="link.active">
                                {{ link.label }}
                            </NavLink>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link v-for="link in iconLinks" :key="link.href" :href="link.href"
                              class="flex items-center text-white hover:text-blue-200 transition duration-150 ease-in-out">
                            <i :class="['pi', link.icon, 'text-xl mr-2']"></i>
                            <span class="text-sm">{{ link.text }}</span>
                        </Link>
                        <div class="relative flex items-center">
                            <button
                                @click="toggleMenu"
                                class="flex items-center space-x-2 text-white hover:text-blue-200 transition duration-150 ease-in-out focus:outline-none"
                            >
                                <Avatar
                                    :label="userInitials"
                                    class="bg-blue-500"
                                    shape="circle"
                                    size="small"
                                />
                                <span class="text-sm font-medium">{{ user.name }}</span>
                                <i class="pi pi-chevron-down text-xs"></i>
                            </button>
                            <Menu ref="menu" :model="userMenuItems" :popup="true" />
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="flex flex-grow pt-16">
            <!-- Sidebar -->
            <div class="w-64 bg-slate-800 shadow-xl z-10 transition-all duration-300 ease-in-out flex-shrink-0 overflow-y-auto h-[calc(100vh-4rem)]">
                <nav class="mt-5">
                    <div v-for="item in menuItems" :key="item.label" class="px-2 py-2">
                        <Link v-if="!item.submenu" :href="route(item.route)" class="flex items-center px-4 py-2 text-sm font-medium text-white hover:bg-blue-600 hover:text-white rounded-md">
                            <i :class="[item.icon, 'mr-3 text-blue-300']"></i>
                            {{ item.label }}
                        </Link>
                        <div v-else>
                            <button @click="toggleSubmenu(item)" class="flex items-center w-full px-4 py-2 text-sm font-medium text-white hover:bg-blue-600 hover:text-white focus:outline-none rounded-md">
                                <i :class="[item.icon, 'mr-3 text-blue-300']"></i>
                                {{ item.label }}
                                <svg class="ml-auto h-5 w-5 text-blue-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="item.isOpen" class="mt-2 space-y-2 px-4">
                                <template v-for="subItem in item.submenu" :key="subItem.label">
                                    <Link
                                        v-if="!subItem.disabled"
                                        :href="route(subItem.route)"
                                        class="block px-4 py-2 text-sm text-blue-200 hover:bg-blue-600 hover:text-white rounded-md"
                                    >
                                        {{ subItem.label }}
                                    </Link>
                                    <span
                                        v-else
                                        class="block px-4 py-2 text-sm text-gray-500 cursor-not-allowed rounded-md"
                                    >
                                        {{ subItem.label }}
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-grow flex flex-col overflow-y-auto h-[calc(100vh-4rem)]">
                <!-- Page Header -->
                <div v-if="showHeader" class="bg-white shadow">
                    <div class="flex justify-between items-center py-4 px-10">
                        <BreadcrumbNav />
                        <slot name="header-actions"></slot>
                    </div>
                </div>
                <!-- Page Content -->
                <main :class="['flex-grow bg-gray-100', { 'pt-6': !showHeader }]">
                    <div class="py-12">
                        <div class="mx-6">
                            <slot />
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {Link, router, usePage} from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import BreadcrumbNav from '@/Components/BreadcrumbNav.vue';

const props = defineProps({
    showHeader: {
        type: Boolean,
        default: true
    }
});

const menuItems = ref([
    { label: 'Dashboard', icon: 'pi pi-home', route: 'dashboard' },
    {
        label: 'Inventory',
        icon: 'pi pi-box',
        submenu: [
            { label: 'Items', route: 'inventory.items' },
            { label: 'Categories', route: 'inventory.categories.index' },
            { label: 'Stock Movements', route: 'inventory.stockMovements' },
            { label: 'Low Stock Alert', route: 'inventory.lowStockAlert', disabled: true },
        ]
    },
    {
        label: 'Sales',
        icon: 'pi pi-shopping-cart',
        submenu: [
            { label: 'Orders', route: 'sales.orders.index' },
            { label: 'Invoices', route: 'sales.invoices', disabled: true },
            { label: 'Customers', route: 'sales.customers', disabled: true },
            { label: 'Returns', route: 'sales.returns', disabled: true },
            { label: 'Point of Sale', route: 'sales.pos', disabled: true },
        ]
    },
    {
        label: 'Purchases',
        icon: 'pi pi-shopping-bag',
        submenu: [
            { label: 'Orders', route: 'purchases.orders' },
            { label: 'Bills', route: 'purchases.bills', disabled: true },
            { label: 'Payments', route: 'purchases.payments', disabled: true },
            { label: 'Returns', route: 'purchases.returns', disabled: true },
        ]
    },
    {
        label: 'Suppliers',
        icon: 'pi pi-truck',
        submenu: [
            { label: 'List All', route: 'suppliers.index' },
            { label: 'Add New', route: 'suppliers.create', disabled: true },
        ]
    },
    {
        label: 'Reports',
        icon: 'pi pi-chart-bar',
        submenu: [
            { label: 'Sales Report', route: 'reports.sales', disabled: true },
            { label: 'Inventory Report', route: 'reports.inventory', disabled: true },
            { label: 'Profit & Loss', route: 'reports.profitLoss', disabled: true },
            { label: 'Tax Report', route: 'reports.tax', disabled: true },
        ]
    },
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        submenu: [
            { label: 'General', route: 'settings.general', disabled: true },
            { label: 'Users & Roles', route: 'settings.users', disabled: true },
            { label: 'Integrations', route: 'settings.integrations', disabled: true },
        ]
    },
]);

const menu = ref();
const toggleSubmenu = (item) => {
    item.isOpen = !item.isOpen;
};

const user = computed(() => usePage().props.auth.user);
const userInitials = computed(() => {
    const names = user.value.name.split(' ');
    return names.map(name => name[0].toUpperCase()).join('');
});

const signOut = () => {
    router.post(route('logout'));
};

const userMenuItems = ref([
    {
        label: 'Sign Out',
        icon: 'pi pi-power-off',
        command: signOut
    }
]);

const toggleMenu = (event) => {
    menu.value.toggle(event);
};
</script>
