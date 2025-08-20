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
                                <Skeleton v-if="isKpiLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.totalProducts }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Stock</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isKpiLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.totalStock }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Total Stock Value</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isKpiLoading" width="8rem" height="2rem" />
                                <template v-else>{{ formatCurrency(kpis.totalStockValue) }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Low Stock Alerts</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isKpiLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.lowStockAlerts }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Orders This Month</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isKpiLoading" width="5rem" height="2rem" />
                                <template v-else>{{ kpis.ordersThisMonth }}</template>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #title>Sales This Month</template>
                        <template #content>
                            <div class="text-3xl font-bold">
                                <Skeleton v-if="isKpiLoading" width="8rem" height="2rem" />
                                <template v-else>{{ formatCurrency(kpis.salesThisMonth) }}</template>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
                    <SalesPurchasesChart :salesData="salesData" :purchasesData="purchasesData" :isLoading="isSalesPurchasesLoading" />
                    <InventoryCategoryChart :inventoryData="inventoryData" :isLoading="isInventoryLoading" />
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <TopSellingProductsChart
                        :topSellingProducts="topSellingProducts"
                        :isLoading="isTopSellingProductsLoading"
                    />
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
import axios from 'axios';
import SalesPurchasesChart from '@/Components/Dashboard/SalesPurchasesChart.vue';
import InventoryCategoryChart from '@/Components/Dashboard/InventoryCategoryChart.vue';
import TopSellingProductsChart from '@/Components/Dashboard/TopSellingProductsChart.vue';

const { formatCurrency } = useCurrencyFormatter();

const isKpiLoading = ref(true);
const isInventoryLoading = ref(true);
const isSalesPurchasesLoading = ref(true);

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
const topSellingProducts = ref([]);
const isTopSellingProductsLoading = ref(true);

const fetchKpiData = async () => {
    try {
        const response = await axios.get(route('dashboard.kpis'));
        if (response.data) {
            kpis.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching KPI data:', error);
    } finally {
        isKpiLoading.value = false;
    }
};

const fetchInventoryData = async () => {
    try {
        const response = await axios.get(route('dashboard.inventory-by-category'));
        if (response.data) {
            inventoryData.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching inventory data:', error);
    } finally {
        isInventoryLoading.value = false;
    }
};

const fetchSalesPurchasesData = async () => {
    try {
        const [salesResponse, purchasesResponse] = await Promise.all([
            axios.get(route('dashboard.sales-over-time')),
            axios.get(route('dashboard.purchases-over-time')),
        ]);
        if (salesResponse.data && purchasesResponse.data) {
            salesData.value = salesResponse.data;
            purchasesData.value = purchasesResponse.data;
        }
    } catch (error) {
        console.error('Error fetching sales and purchases data:', error);
    } finally {
        isSalesPurchasesLoading.value = false;
    }
};

const fetchTopSellingProductsData = async () => {
    try {
        const response = await axios.get(route('dashboard.top-selling-products'));
        if (response.data) {
            topSellingProducts.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching top selling products data:', error);
    } finally {
        isTopSellingProductsLoading.value = false;
    }
};

const fetchDashboardData = () => {
    fetchKpiData();
    fetchInventoryData();
    fetchSalesPurchasesData();
    fetchTopSellingProductsData();
};

onMounted(() => {
    fetchDashboardData();
});
</script>
