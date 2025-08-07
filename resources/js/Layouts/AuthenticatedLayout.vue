<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="flex items-center justify-center h-16 border-b">
                <ApplicationLogo class="w-12 h-12" />
            </div>
            <nav class="mt-5">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </NavLink>
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                            <i class="fas fa-building mr-2"></i> Inventory
                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('inventory.items')">
                            Items
                        </DropdownLink>
<!--                        <DropdownLink :href="route('inventory.items')">-->
<!--                            Item Groups-->
<!--                        </DropdownLink>-->
<!--                        <DropdownLink :href="route('inventory.items')">-->
<!--                            Stock Adjustments-->
<!--                        </DropdownLink>-->
<!--                        <DropdownLink :href="route('inventory.items')">-->
<!--                            Stock History-->
<!--                        </DropdownLink>-->
                    </template>
                </Dropdown>
<!--                <NavLink :href="route('contacts.index')" :active="route().current('contacts.*')">-->
<!--                    <i class="fas fa-address-book mr-2"></i> Contacts-->
<!--                </NavLink>-->
<!--                <NavLink :href="route('inventory.report')" :active="route().current('inventory.report')">-->
<!--                    <i class="fas fa-chart-bar mr-2"></i> Reports-->
<!--                </NavLink>-->
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        <slot name="header" />
                    </h2>
                    <Dropdown align="right" width="48">
                        <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                        </template>

                        <template #content>
<!--                            <DropdownLink-->
<!--                                :href="route('profile.edit')"-->
<!--                            >-->
<!--                                Profile-->
<!--                            </DropdownLink>-->
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="mx-auto px-6 py-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
