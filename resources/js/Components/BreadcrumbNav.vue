<template>
    <nav class="breadcrumbs">
        <ol class="flex items-center space-x-2">
            <li v-for="(crumb, index) in crumbs" :key="index" class="flex items-center">
                <template v-if="index > 0">
                    <i class="pi pi-chevron-right text-gray-400 ml-2 mr-4"></i>
                </template>
                <Link
                    v-if="crumb.href"
                    :href="crumb.href"
                    class="hover:underline"
                >
                    <i v-if="crumb.icon" :class="[crumb.icon, 'mr-1']"></i>
                    {{ crumb.label }}
                </Link>
                <span v-else :class="{ 'text-gray-500': index === crumbs.length - 1 }">
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

const pathIcons = {
    inventory: 'pi pi-box',
    sales: 'pi pi-shopping-cart',
    purchases: 'pi pi-shopping-bag',
    suppliers: 'pi pi-truck',
    reports: 'pi pi-chart-bar',
    settings: 'pi pi-cog'
}

const crumbs = computed(() => {
    const currentUrl = page.url
    console.log('Current URL:', currentUrl)

    const urlParts = currentUrl.split('/').filter(Boolean)
    let items = [{ label: 'Dashboard', href: '/dashboard' }]

    let currentPath = ''
    for (let i = 0; i < urlParts.length; i++) {
        const part = urlParts[i]
        currentPath += `/${part}`
        const label = part.charAt(0).toUpperCase() + part.slice(1).replace(/-/g, ' ')

        if (i === 0) {
            // First path segment after Dashboard, include icon
            items.push({
                label,
                icon: pathIcons[part] || 'pi pi-circle-fill' // Default icon if not found
            })
        } else if (i === urlParts.length - 1) {
            // Last item (current page), don't include href
            items.push({ label })
        } else {
            // Other items, include href
            items.push({ label, href: currentPath })
        }
    }

    return items
})
</script>
