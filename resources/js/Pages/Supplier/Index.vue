<template>
    <SupplierLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suppliers</h2>
        </template>

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
    </SupplierLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SupplierLayout from '@/Layouts/SupplierLayout.vue';
import { Link } from '@inertiajs/vue3';

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
