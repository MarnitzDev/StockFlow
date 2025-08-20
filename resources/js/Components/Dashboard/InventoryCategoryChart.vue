<template>
    <Card>
        <template #title>Inventory by Category</template>
        <template #content>
            <div class="w-full h-[400px]">
                <template v-if="isLoading">
                    <div class="flex items-center w-full h-full">
                        <!-- Pie chart skeleton -->
                        <div class="w-2/3 h-full flex items-center justify-center">
                            <div class="w-96 h-96 rounded-full bg-gray-200 animate-pulse"></div>
                        </div>
                        <!-- Legend skeleton -->
                        <div class="w-1/3 h-full flex flex-col justify-center space-y-2">
                            <div v-for="i in 5" :key="i" class="flex items-center">
                                <div class="w-4 h-4 rounded-sm bg-gray-200 animate-pulse mr-2"></div>
                                <div class="w-20 h-4 bg-gray-200 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <Chart v-else type="pie" :data="chartData" :options="chartOptions" class="h-full" />
            </div>
        </template>
    </Card>
</template>

<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
    inventoryData: Array,
    isLoading: Boolean,
});

const chartData = ref(null);
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const categoryData = props.inventoryData[context.dataIndex];
                    let lines = [`Total Products: ${context.formattedValue}`];
                    if (categoryData.children && categoryData.children.length > 0) {
                        lines.push(`Subcategories:`);
                        categoryData.children.forEach(child => {
                            lines.push(` - ${child.name}`);
                        });
                    }
                    return lines;
                }
            },
            padding: 32,
            bodySpacing: 8,
            titleFont: {
                size: 16,
                weight: 'bold'
            },
            bodyFont: {
                size: 14
            },
            displayColors: false
        }
    }
});

const processChartData = () => {
    chartData.value = {
        labels: props.inventoryData.map(item => item.category),
        datasets: [{
            data: props.inventoryData.map(item => item.count),
            backgroundColor: [
                '#6C63FF', '#FF6584', '#F9A826', '#48CAE4', '#20C997',
                '#FFA07A', '#9D84B7', '#FF9AA2', '#B5EAD7', '#C7CEEA'
            ]
        }],
    };
};

watch(() => props.inventoryData, processChartData, { immediate: true });
</script>
