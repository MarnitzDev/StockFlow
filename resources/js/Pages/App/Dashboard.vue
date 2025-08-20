<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :show-header="false">
        <div class="pb-12">
            <div class="px-6">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    <Card>
                        <template #title>Total Products</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.totalProducts }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Stock</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.totalStock }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Stock Value</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isLoading" width="8rem" height="2rem" />
                                <template v-else>{{ formatCurrency(kpis.totalStockValue) }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Low Stock Alerts</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.lowStockAlerts }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Orders This Month</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.ordersThisMonth }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Sales This Month</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isLoading" width="8rem" height="2rem" />
                                <template v-else>{{ formatCurrency(kpis.salesThisMonth) }}</template>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
                    <Card>
                        <template #title>Sales and Purchases Over Time</template>
                        <template #content>
                            <Chart type="bar" :data="salesPurchasesData" :options="barChartOptions" class="h-[30rem]" />
                        </template>
                    </Card>
                    <Card>
                        <template #title>Inventory by Category</template>
                        <template #content>
                            <div class="w-full h-[400px]"> <!-- Increased width and set a fixed height -->
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
                                <Chart v-else type="pie" :data="inventoryData" :options="pieChartOptions" class="h-full" />
                            </div>
                        </template>
                    </Card>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <Card>
                        <template #title>Top Selling Products</template>
                        <template #content>
                            <Chart type="bar" :data="topSellingProducts" />
                        </template>
                    </Card>
                    <Card>
                        <template #title>Low Stock Alerts</template>
                        <template #content>
                            <DataTable :value="lowStockItems" :paginator="true" :rows="5">
                                <Column field="name" header="Product Name"></Column>
                                <Column field="sku" header="SKU"></Column>
                                <Column field="stock" header="Current Stock"></Column>
                                <Column field="reorderLevel" header="Reorder Level"></Column>
                            </DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import axios from 'axios';
import Chart from 'primevue/chart';

const { formatCurrency } = useCurrencyFormatter();

const isLoading = ref(true);
const kpis = ref({
    totalProducts: 0,
    totalStock: 0,
    totalStockValue: 0,
    salesThisMonth: 0,
    ordersThisMonth: 0,
    lowStockAlerts: 0
});

const salesPurchasesData = ref(null);
const inventoryData = ref(null);
const barChartOptions = ref(null);
const pieChartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const categoryData = inventoryData.value.rawData[context.dataIndex];
                    let lines = [`${context.label}: ${context.formattedValue}`];
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

const fetchDashboardData = async () => {
    isLoading.value = true;
    try {
        const [kpiResponse, inventoryResponse, salesResponse, purchasesResponse] = await Promise.all([
            axios.get(route('dashboard.kpis')),
            axios.get(route('dashboard.inventory-by-category')),
            axios.get(route('dashboard.sales-over-time')),
            axios.get(route('dashboard.purchases-over-time')),
        ]);

        // Process KPI data
        if (kpiResponse.data) {
            kpis.value = kpiResponse.data;
        }

        // Process inventory data
        if (inventoryResponse.data) {
            inventoryData.value = {
                labels: inventoryResponse.data.map(item => item.category),
                datasets: [{
                    data: inventoryResponse.data.map(item => item.count),
                    backgroundColor: [
                        '#6C63FF', '#FF6584', '#F9A826', '#48CAE4', '#20C997',
                        '#FFA07A', '#9D84B7', '#FF9AA2', '#B5EAD7', '#C7CEEA'
                    ]
                }],
                rawData: inventoryResponse.data
            };
        }

        // Process sales and purchases data
        if (salesResponse.data && purchasesResponse.data) {
            const allMonths = [...new Set([
                ...salesResponse.data.map(item => item.month),
                ...purchasesResponse.data.map(item => item.month)
            ])].sort();

            const salesDataMap = new Map(salesResponse.data.map(item => [item.month, parseFloat(item.total)]));
            const purchasesDataMap = new Map(purchasesResponse.data.map(item => [item.month, parseFloat(item.total)]));

            const salesDataArray = allMonths.map(month => salesDataMap.get(month) || 0);
            const purchasesDataArray = allMonths.map(month => purchasesDataMap.get(month) || 0);

            salesPurchasesData.value = {
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

            setBarChartOptions();
        }

    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    } finally {
        isLoading.value = false;
    }
};

const setBarChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    barChartOptions.value = {
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

onMounted(() => {
    fetchDashboardData();
});
</script>
