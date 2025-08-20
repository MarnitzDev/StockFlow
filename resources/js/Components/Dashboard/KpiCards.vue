<template>
    <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <Card v-for="(kpi, index) in kpiData" :key="index">
            <template #title>{{ kpi.title }}</template>
            <template #content>
                <div class="text-3xl font-bold">
                    <Skeleton v-if="isLoading" :width="kpi.skeletonWidth" height="2rem" />
                    <template v-else>
                        {{ kpi.formatter ? kpi.formatter(kpi.value) : kpi.value }}
                    </template>
                </div>
            </template>
        </Card>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
    kpiData: {
        type: Array,
        required: true
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});
</script>
