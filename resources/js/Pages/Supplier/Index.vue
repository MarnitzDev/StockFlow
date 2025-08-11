<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Supplier {
    id: number;
    name: string;
    image: {
        image_path: string;
    };
}

defineProps<{
    suppliers: Supplier[];
}>();
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Head title="Suppliers" />

        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
<!--                            <Link :href="route('welcome')">-->
<!--                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />-->
<!--                            </Link>-->
                        </div>
                    </div>
<!--                    <div class="flex items-center">-->
<!--                        <Link :href="route('dashboard')" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</Link>-->
<!--                    </div>-->
                </div>
            </div>
        </nav>

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suppliers</h2>
            </div>
        </header>

        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <Link
                                v-for="supplier in suppliers"
                                :key="supplier.id"
                                :href="route('supplier.show', supplier.id)"
                                class="bg-white border rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col"
                            >
                                <div class="h-48 overflow-hidden">
                                    <img
                                        :src="supplier.image"
                                        :alt="supplier.name"
                                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                    />
                                </div>
                                <div class="p-4 flex-grow flex items-center justify-center">
                                    <h3 class="font-bold text-lg text-center text-gray-800">{{ supplier.name }}</h3>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
