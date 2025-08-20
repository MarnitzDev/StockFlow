<template>
    <Card>
        <template #title>Sales and Purchases Over Time</template>
        <template #content>
            <div class="h-[400px]">
                <template v-if="isLoading">
                    <div class="flex flex-col h-full">
                        <!-- Legend skeleton -->
                        <div class="mt-4 flex justify-center space-x-4">
                            <div v-for="i in 2" :key="i" class="flex items-center">
                                <div class="w-4 h-4 rounded-sm bg-gray-200 animate-pulse mr-2"></div>
                                <div class="w-20 h-4 bg-gray-200 animate-pulse"></div>
                            </div>
                        </div>
                        <!-- Bar chart skeleton -->
                        <div class="flex-grow flex items-end space-x-2 mb-4">
                            <div v-for="i in 12" :key="i" class="w-1/12 bg-gray-200 animate-pulse" :style="{ height: `${Math.random() * 100}%` }"></div>
                        </div>
                    </div>
                </template>
                <Chart v-else type="bar" :data="chartData" :options="chartOptions" class="h-full" />
            </div>
        </template>
    </Card>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const props = defineProps({
    salesData: Array,
    purchasesData: Array,
    isLoading: Boolean,
});

const { formatCurrency } = useCurrencyFormatter();
const chartData = ref(null);
const chartOptions = ref(null);

const processChartData = () => {
    const allMonths = [...new Set([
        ...props.salesData.map(item => item.month),
        ...props.purchasesData.map(item => item.month)
    ])].sort();

    const salesDataMap = new Map(props.salesData.map(item => [item.month, parseFloat(item.total)]));
    const purchasesDataMap = new Map(props.purchasesData.map(item => [item.month, parseFloat(item.total)]));

    const salesDataArray = allMonths.map(month => salesDataMap.get(month) || 0);
    const purchasesDataArray = allMonths.map(month => purchasesDataMap.get(month) || 0);

    chartData.value = {
        labels: allMonths.map(month => {
            const date = new Date(month);
            return date.toLocaleString('default', { month: 'short', year: 'numeric' });
        }),
        datasets: [
            {
                label: 'Sales',
                backgroundColor: '#20C997',
                data: salesDataArray
            },
            {
                label: 'Purchases',
                backgroundColor: '#FF6584',
                data: purchasesDataArray
            }
        ]
    };

    setChartOptions();
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    chartOptions.value = {
        maintainAspectRatio: false,
        aspectRatio: 0.8,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += formatCurrency(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary,
                    font: {
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary,
                    callback: function(value) {
                        return formatCurrency(value);
                    }
                },
                grid: {
                    color: surfaceBorder,
                    drawBorder: false
                }
            }
        }
    };
};

watch(() => [props.salesData, props.purchasesData], processChartData, { immediate: true });

onMounted(() => {
    processChartData();
});
</script>
