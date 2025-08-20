<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout :show-header="false">
        <div class="pb-12">
            <div class="px-6">
                <KpiCards :kpiData="kpiData" :isLoading="isKpiLoading" />
                <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
                    <SalesPurchasesChart :salesData="salesData" :purchasesData="purchasesData" :isLoading="isSalesPurchasesLoading" />
                    <InventoryCategoryChart :inventoryData="inventoryData" :isLoading="isInventoryLoading" />
                </div>
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <TopSellingProductsChart :topSellingProducts="topSellingProducts" :isLoading="isTopSellingProductsLoading" />
                    <LowStockAlertsTable :lowStockItems="lowStockItems" :isLoading="isLowStockItemsLoading" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PrimeIcons } from '@primevue/core/api';
import { useCurrencyFormatter } from '@/Composables/useCurrencyFormatter';
import KpiCards from '@/Components/Dashboard/KpiCards.vue';
import SalesPurchasesChart from '@/Components/Dashboard/SalesPurchasesChart.vue';
import InventoryCategoryChart from '@/Components/Dashboard/InventoryCategoryChart.vue';
import TopSellingProductsChart from '@/Components/Dashboard/TopSellingProductsChart.vue';
import LowStockAlertsTable from '@/Components/Dashboard/LowStockAlertsTable.vue';

const { formatCurrency } = useCurrencyFormatter();

const isKpiLoading = ref(true);
const isInventoryLoading = ref(true);
const isSalesPurchasesLoading = ref(true);
const isTopSellingProductsLoading = ref(true);
const isLowStockItemsLoading = ref(true);

const kpis = ref({
    totalProducts: 0,
    totalStock: 0,
    totalStockValue: 0,
    salesLastMonth: 0,
    ordersLastMonth: 0,
    lowStockAlerts: 0,
    changes: {}
});

const salesData = ref([]);
const purchasesData = ref([]);
const inventoryData = ref([]);
const topSellingProducts = ref([]);
const lowStockItems = ref([]);

const kpiData = computed(() => [
    {
        title: 'Total Products',
        value: kpis.value.totalProducts,
        icon: PrimeIcons.BOX,
        iconColor: 'text-blue-500',
        change: kpis.value.changes?.totalProducts ?? 0
    },
    {
        title: 'Total Stock',
        value: kpis.value.totalStock,
        icon: PrimeIcons.SHOPPING_CART,
        iconColor: 'text-green-500',
        change: kpis.value.changes?.totalStock ?? 0
    },
    {
        title: 'Total Stock Value',
        value: kpis.value.totalStockValue,
        formatter: formatCurrency,
        icon: PrimeIcons.DOLLAR,
        iconColor: 'text-yellow-500',
        change: kpis.value.changes?.totalStockValue ?? 0
    },
    {
        title: 'Low Stock Alerts',
        value: kpis.value.lowStockAlerts,
        icon: PrimeIcons.EXCLAMATION_TRIANGLE,
        iconColor: 'text-red-500',
        change: kpis.value.changes?.lowStockAlerts ?? 0,
        reverseChangeColor: true
    },
    {
        title: 'Orders Last Month',
        value: kpis.value.ordersLastMonth,
        icon: PrimeIcons.SHOPPING_BAG,
        iconColor: 'text-purple-500',
        change: kpis.value.changes?.ordersLastMonth ?? 0
    },
    {
        title: 'Sales Last Month',
        value: kpis.value.salesLastMonth,
        formatter: formatCurrency,
        icon: PrimeIcons.MONEY_BILL,
        iconColor: 'text-green-500',
        change: kpis.value.changes?.salesLastMonth ?? 0
    }
]);

const fetchKpiData = async () => {
    try {
        const response = await axios.get(route('dashboard.kpis'));
        kpis.value = {
            ...response.data.currentData,
            changes: response.data.changes
        };
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

const fetchLowStockItems = async () => {
    try {
        const response = await axios.get(route('dashboard.low-stock-items'));
        if (response.data) {
            lowStockItems.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching low stock items:', error);
    } finally {
        isLowStockItemsLoading.value = false;
    }
};

const fetchDashboardData = () => {
    fetchKpiData();
    fetchInventoryData();
    fetchSalesPurchasesData();
    fetchTopSellingProductsData();
    fetchLowStockItems();
};

onMounted(() => {
    fetchDashboardData();
});
</script>
