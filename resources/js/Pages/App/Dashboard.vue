<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// Mock data (replace with real data from your backend)
const kpis = ref({
    totalProducts: 0,
    totalStock: 0,
    salesThisMonth: 0,
    ordersThisMonth: 0,
    lowStockAlerts: 0
});

const salesData = ref(null);
const inventoryData = ref(null);
const topSellingProducts = ref(null);
const lowStockItems = ref([]);

onMounted(() => {
    // Fetch data from your backend here
    // For now, we'll use mock data
    kpis.value = {
        totalProducts: 1250,
        totalStock: 75000,
        salesThisMonth: 125000,
        ordersThisMonth: 450,
        lowStockAlerts: 15
    };

    salesData.value = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                label: 'Sales',
                data: [65000, 59000, 80000, 81000, 56000, 55000, 40000],
                fill: false,
                borderColor: '#4bc0c0'
            }
        ]
    };

    inventoryData.value = {
        labels: ['Shoes', 'Clothes', 'Electronics', 'Home & Garden', 'Sports'],
        datasets: [
            {
                data: [300, 50, 100, 80, 120],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }
        ]
    };

    topSellingProducts.value = {
        labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
        datasets: [
            {
                label: 'Units Sold',
                data: [300, 250, 200, 150, 100],
                backgroundColor: '#36A2EB'
            }
        ]
    };

    lowStockItems.value = [
        { name: 'Product X', sku: 'SKU001', stock: 5, reorderLevel: 10 },
        { name: 'Product Y', sku: 'SKU002', stock: 3, reorderLevel: 15 },
        { name: 'Product Z', sku: 'SKU003', stock: 8, reorderLevel: 20 }
    ];
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :show-header="false">
        <div class="pb-12">
            <div class="px-6">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-5">
                    <Card>
                        <template #title>Total Products</template>
                        <template #content>
                            <div class="text-3xl font-bold">{{ kpis.totalProducts }}</div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Stock</template>
                        <template #content>
                            <div class="text-3xl font-bold">{{ kpis.totalStock }}</div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Sales This Month</template>
                        <template #content>
                            <div class="text-3xl font-bold">${{ kpis.salesThisMonth.toLocaleString() }}</div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Orders This Month</template>
                        <template #content>
                            <div class="text-3xl font-bold">{{ kpis.ordersThisMonth }}</div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Low Stock Alerts</template>
                        <template #content>
                            <div class="text-3xl font-bold">{{ kpis.lowStockAlerts }}</div>
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
                            <div class="w-1/2 mx-auto">
                                <Chart type="pie" :data="inventoryData" :options="pieChartOptions" />
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
