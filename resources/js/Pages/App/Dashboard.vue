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
                        <template #title>Sales Over Time</template>
                        <template #content>
                            <Chart type="line" :data="salesData" />
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
import { ref, onMounted } from 'vue';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';

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

const salesData = ref(null);
const inventoryData = ref(null);
const topSellingProducts = ref(null);
const lowStockItems = ref([]);

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
                    let lines = [];
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
    try {
        const [kpiResponse, inventoryResponse, salesResponse, topSellingResponse, lowStockResponse] = await Promise.all([
            axios.get(route('dashboard.kpis')),
            axios.get(route('dashboard.inventory-by-category')),
            // axios.get(route('dashboard.sales-over-time')),
            // axios.get(route('dashboard.top-selling-products')),
            // axios.get(route('dashboard.low-stock-items'))
        ]);

        kpis.value = kpiResponse.data;

        inventoryData.value = {
            labels: inventoryResponse.data.map(item => item.category),
            datasets: [{
                data: inventoryResponse.data.map(item => item.count),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#FF6384', '#C9CBCF', '#7BC8A4', '#E7E9ED'
                ]
            }],
            rawData: inventoryResponse.data
        };

        salesData.value = {
            labels: salesResponse.data.map(item => item.month),
            datasets: [{
                label: 'Sales',
                data: salesResponse.data.map(item => item.total),
                fill: false,
                borderColor: '#4bc0c0'
            }]
        };

        topSellingProducts.value = {
            labels: topSellingResponse.data.map(item => item.name),
            datasets: [{
                label: 'Units Sold',
                data: topSellingResponse.data.map(item => item.units_sold),
                backgroundColor: '#36A2EB'
            }]
        };

        lowStockItems.value = lowStockResponse.data;

        isLoading.value = false;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        isLoading.value = false;
        // Handle error (e.g., show an error message to the user)
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>
