<template>
    <nav class="breadcrumbs">
        <ol class="flex space-x-2">
            <li v-for="(crumb, index) in crumbs" :key="index" class="flex items-center">
                <template v-if="index > 0">
                    <span class="mx-2 text-gray-500">/</span>
                </template>
                <Link
                    v-if="crumb.href"
                    :href="crumb.href"
                    class="text-blue-600 hover:text-blue-800"
                >
                    <i v-if="crumb.icon" :class="[crumb.icon, 'mr-1']"></i>
                    {{ crumb.label }}
                </Link>
                <span v-else class="text-gray-500">
                    <i v-if="crumb.icon" :class="[crumb.icon, 'mr-1']"></i>
                    {{ crumb.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()

const crumbs = computed(() => {
    const currentUrl = page.url
    console.log('Current URL:', currentUrl)

    if (!currentUrl) {
        return [{ label: 'Dashboard', icon: 'pi pi-home', href: '/' }]
    }

    const urlParts = currentUrl.split('/').filter(Boolean)
    let items = [{ label: 'Dashboard', icon: 'pi pi-home', href: '/' }]

    let currentPath = ''
    for (let i = 0; i < urlParts.length; i++) {
        const part = urlParts[i]
        currentPath += `/${part}`
        const label = part.charAt(0).toUpperCase() + part.slice(1).replace(/-/g, ' ')
        items.push({ label, href: currentPath })
    }

    return items
})
</script>
