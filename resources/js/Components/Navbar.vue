<template>
    <nav class="bg-blue-700 border-b border-blue-600 sticky top-0 z-50">
        <div class="px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="shrink-0 flex items-center">
                        <div class="flex items-center">
                            <div class="shrink-0 flex items-center">
                                <div class="flex items-center">
                                    <Link :href="route('home')" class="flex items-center">
                                        <span class="font-['Outfit'] text-white text-xl font-bold mr-1">StockFlow</span>
                                    </Link>
                                </div>
                            </div>
                            <span class="bg-white text-blue-700 text-xs font-semibold px-2 py-1 rounded">{{ layoutName }}</span>
                        </div>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <NavLink v-for="link in navLinks" :key="link.href" :href="link.href" :active="link.active" class="text-white">
                            {{ link.label }}
                        </NavLink>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <Link
                        v-for="icon in iconLinks"
                        :key="icon.href"
                        :href="icon.href"
                        class="flex items-center text-white hover:text-blue-200 transition duration-150 ease-in-out"
                    >
                        <i :class="['pi', icon.icon, 'text-xl mr-2']"></i>
                        <span class="text-sm">{{ icon.text }}</span>
                    </Link>
                    <div class="relative flex items-center">
                        <button
                            @click="toggleMenu"
                            class="flex items-center space-x-2 text-white hover:text-blue-200 transition duration-150 ease-in-out focus:outline-none"
                        >
                            <Avatar
                                :label="userInitials"
                                class="bg-blue-500"
                                shape="circle"
                                size="small"
                            />
                            <span class="text-sm font-medium">{{ user.name }}</span>
                            <i class="pi pi-chevron-down text-xs"></i>
                        </button>
                        <Menu ref="menu" :model="userMenuItems" :popup="true" />
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    layoutName: {
        type: String,
        required: true
    },
    navLinks: {
        type: Array,
        required: true
    },
    iconLinks: {
        type: Array,
        required: true
    }
});

const user = computed(() => usePage().props.auth.user);
const userInitials = computed(() => {
    const names = user.value.name.split(' ');
    return names.map(name => name[0].toUpperCase()).join('');
});
const menu = ref();

const signOut = () => {
    router.post(route('logout'));
};

const userMenuItems = ref([
    {
        label: 'Sign Out',
        icon: 'pi pi-power-off',
        command: signOut
    }
]);

const toggleMenu = (event) => {
    menu.value.toggle(event);
};
</script>

<style scoped>
.nav-link {
    @apply inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out;
}

.nav-link:not(.active) {
    @apply border-transparent text-blue-100 hover:text-white hover:border-blue-300;
}

.nav-link.active {
    @apply border-white text-white;
}
</style>
