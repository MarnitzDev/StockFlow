<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block py-2 px-4 text-sm font-medium text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600'
        : 'block py-2 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
