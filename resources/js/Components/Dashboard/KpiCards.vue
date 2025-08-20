<template>
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <Card v-for="(kpi, index) in kpiData" :key="index" class="bg-white shadow-md">
            <template #title>
                <div class="flex items-center justify-center mb-2 text-center">
                    <i :class="['mr-2 text-xl', kpi.icon, kpi.iconColor]"></i>
                    <h3 class="text-lg font-semibold text-gray-700">{{ kpi.title }}</h3>
                </div>
            </template>
            <template #content>
                <div class="flex flex-col items-center">
                    <template v-if="isLoading">
                        <Skeleton width="6rem" height="2.5rem" class="mb-2" />
                        <Skeleton width="10rem" height="1rem" />
                    </template>
                    <template v-else>
                        <div :class="['text-2xl font-semibold', kpi.valueColor]">
                            {{ kpi.formatter ? kpi.formatter(kpi.value) : kpi.value }}
                        </div>
                        <div v-if="kpi.change !== undefined" :class="['text-sm mt-1', getChangeColor(kpi.change, kpi.reverseChangeColor)]">
                            {{ getChangeArrow(kpi.change, kpi.reverseChangeColor) }} {{ Math.abs(kpi.change) }}% previous month
                        </div>
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

const getChangeColor = (change, reverse = false) => {
    if (change === 0) return 'text-gray-600';
    const isPositive = reverse ? change < 0 : change > 0;
    return isPositive ? 'text-green-600' : 'text-red-600';
};

const getChangeArrow = (change, reverse = false) => {
    if (change === 0) return '→';
    const isPositive = reverse ? change < 0 : change > 0;
    return isPositive ? '↑' : '↓';
};
</script>
