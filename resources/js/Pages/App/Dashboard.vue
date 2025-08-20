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
                    <SalesPurchasesChart :salesData="salesData" :purchasesData="purchasesData" :isLoading="isLoading" />
                    <InventoryCategoryChart :inventoryData="inventoryData" :isLoading="isLoading" />
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
import SalesPurchasesChart from '@/Components/Dashboard/SalesPurchasesChart.vue';
import InventoryCategoryChart from '@/Components/Dashboard/InventoryCategoryChart.vue';

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

const salesData = ref([]);
const purchasesData = ref([]);
const inventoryData = ref([]);

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
            inventoryData.value = inventoryResponse.data;
        }

        // Process sales and purchases data
        if (salesResponse.data && purchasesResponse.data) {
            salesData.value = salesResponse.data;
            purchasesData.value = purchasesResponse.data;
        }

    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>
