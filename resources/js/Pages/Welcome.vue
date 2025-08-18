<template>
    <Head title="Welcome to StockFlow" />
    <div class="bg-gray-100 min-h-screen">
        <!-- Full-width header section -->
        <div class="bg-gradient-to-br from-indigo-500 via-blue-700 to-indigo-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 leading-tight tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-200 font-['Outfit']">
                            Welcome to StockFlow
                        </span>
                        </h1>
                        <p class="text-xl mb-8 opacity-90">
                            An all-in-one platform for streamlined inventory management, supplier tracking, and real-time sales monitoring.
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="flex justify-center items-center">
                            <img src="/images/business_report.svg" alt="StockFlow Dashboard" class="w-2/3 h-auto p-4" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <Card class="flex flex-col h-full py-6">
                    <template #header>
                        <div class="flex justify-center items-center h-32">
                            <img alt="Inventory Management" src="/images/dashboard.gif" class="max-w-full max-h-full object-contain" />
                        </div>
                    </template>
                    <template #title>
                        <h3 class="text-xl font-semibold text-center">Inventory Management</h3>
                    </template>
                    <template #content>
                        <div class="flex flex-col h-full">
                            <p class="mb-4 text-center flex-grow p-2">
                                Manage stock levels, track movements, and gain insights with real-time analytics
                            </p>
                        </div>
                    </template>
                    <template #footer>
                        <div class="flex justify-center mt-auto">
                            <Link :href="route('dashboard')">
                                <Link :href="route('dashboard')">
                                    <FeatureButton
                                        label="View Dashboard"
                                        icon="pi pi-chart-line"
                                    />
                                </Link>
                            </Link>
                        </div>
                    </template>
                </Card>

                <Card class="flex flex-col h-full py-6">
                    <template #header>
                        <div class="flex justify-center items-center h-32">
                            <img alt="Vendor Management" src="/images/inventory.gif" class="max-w-full max-h-full object-contain" />
                        </div>
                    </template>
                    <template #title>
                        <h3 class="text-xl font-semibold text-center">Vendor Management</h3>
                    </template>
                    <template #content>
                        <div class="flex flex-col h-full">
                            <p class="mb-4 text-center flex-grow p-2">
                                Streamline suppliers, manage purchase orders, and optimize procurement
                            </p>
                        </div>
                    </template>
                    <template #footer>
                        <div class="flex justify-center mt-auto">
                            <Link :href="route('vendors.index')">
                                <FeatureButton
                                    label="Manage Stock"
                                    icon="pi pi-truck"
                                />
                            </Link>
                        </div>
                    </template>
                </Card>

                <Card class="flex flex-col h-full py-6">
                    <template #header>
                        <div class="flex justify-center items-center h-32">
                            <img alt="Point of Sale" src="/images/online-shopping.gif" class="max-w-full max-h-full object-contain" />
                        </div>
                    </template>
                    <template #title>
                        <h3 class="text-xl font-semibold text-center">Point of Sale (POS)</h3>
                    </template>
                    <template #content>
                        <div class="flex flex-col h-full">
                            <p class="mb-4 text-center flex-grow p-2">
                                Sell from your inventory with our POS — customers browse, order, and pay seamlessly.
                            </p>
                        </div>
                    </template>
                    <template #footer>
                        <div class="flex justify-center mt-auto">
                            <Link :href="route('pos.index')">
                                <FeatureButton
                                    label="Open POS"
                                    icon="pi pi-shopping-cart"
                                />
                            </Link>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Card from 'primevue/card';
import FeatureButton from '@/Components/FeatureButton.vue';


defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>
