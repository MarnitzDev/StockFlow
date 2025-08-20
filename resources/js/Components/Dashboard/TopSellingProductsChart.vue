<template>
    <Card>
        <template #title>Top Selling Products</template>
        <template #content>
            <div class="h-[400px]">
                <template v-if="isLoading">
                    <div class="flex flex-col h-full justify-between py-4">
                        <div v-for="i in 5" :key="i" class="flex items-center">
                            <div class="w-1/3 h-4 bg-gray-200 animate-pulse mr-4"></div>
                            <div class="flex-grow">
                                <div class="h-16 bg-gray-200 animate-pulse" :style="{ width: `${Math.random() * 60 + 40}%` }"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <Chart v-else type="bar" :data="chartData" :options="chartOptions" class="h-full" />
            </div>
        </template>
    </Card>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

const props = defineProps({
    topSellingProducts: Array,
    isLoading: Boolean,
});

const { formatCurrency } = useCurrencyFormatter();
const chartData = ref(null);
const chartOptions = ref({
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const product = props.topSellingProducts[context.dataIndex];
                    return [
                        `Sales: ${formatCurrency(product.total_sales)}`,
                        `Units Sold: ${product.total_quantity}`
                    ];
                }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                callback: function(value) {
                    return formatCurrency(value);
                }
            }
        }
    }
});

const processChartData = () => {
    if (props.topSellingProducts) {
        chartData.value = {
            labels: props.topSellingProducts.map(product => product.name),
            datasets: [{
                label: 'Sales',
                data: props.topSellingProducts.map(product => product.total_sales),
                backgroundColor: '#20C997',
            }]
        };
    }
};

watch(() => props.topSellingProducts, processChartData, { immediate: true });
</script>
