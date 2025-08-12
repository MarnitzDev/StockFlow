<template>
    <VendorLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vendors</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Link
                            v-for="vendor in vendors"
                            :key="vendor.id"
                            :href="route('vendor.show', vendor.id)"
                            class="bg-white border rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col"
                        >
                            <div class="h-48 overflow-hidden">
                                <img
                                    :src="vendor.image"
                                    :alt="vendor.name"
                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                />
                            </div>
                            <div class="p-4 flex-grow flex items-center justify-center">
                                <h3 class="font-bold text-lg text-center text-gray-800">{{ vendor.name }}</h3>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Link } from '@inertiajs/vue3';

interface vendor {
    id: number;
    name: string;
    image: {
        image_path: string;
    };
}

defineProps<{
    vendors: vendor[];
}>();
</script>
