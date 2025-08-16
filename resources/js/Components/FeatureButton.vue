<template>
    <Button
        :label="label"
        :icon="icon"
        :iconPos="iconPos"
        class="feature-button"
        v-bind="$attrs"
    />
</template>

<script setup>
import Button from 'primevue/button';

defineProps({
    label: String,
    icon: String,
    iconPos: {
        type: String,
        default: 'left'
    }
});
</script>

<style scoped>
.feature-button {
    background-color: #4f46e5 !important; /* indigo-600 */
    color: white !important;
    font-weight: 600 !important;
    padding: 0.75rem 1.5rem !important;
    border-radius: 9999px !important;
    transition: all 0.3s ease-in-out !important;
    display: inline-flex !important;
    align-items: center !important;
    border: none !important;
}

.feature-button:hover {
    background-color: #4338ca !important; /* indigo-700 for a slightly darker shade on hover */
    transform: translateY(-1px) scale(1.05) !important;
}
</style>
