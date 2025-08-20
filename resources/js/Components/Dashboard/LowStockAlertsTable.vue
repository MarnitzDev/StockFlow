<template>
    <Card>
        <template #title>Low Stock Alerts</template>
        <template #content>
            <DataTable :value="isLoading ? loadingData : lowStockItems" :paginator="true" :rows="5">
                <Column field="name" header="Product Name">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="100%" height="1.5rem" />
                        <template v-else>{{ data.name }}</template>
                    </template>
                </Column>
                <Column field="stock" header="Stock">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="100%" height="1.5rem" />
                        <template v-else>{{ data.stock }}</template>
                    </template>
                </Column>
                <Column field="low_stock_threshold" header="Threshold">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="100%" height="1.5rem" />
                        <template v-else>{{ data.low_stock_threshold }}</template>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>

<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
    lowStockItems: {
        type: Array,
        required: true
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});

const loadingData = computed(() => Array(5).fill({}));
</script>
