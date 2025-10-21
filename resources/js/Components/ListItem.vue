<script setup>
import { NavigationMenuLink } from '@/components/ui/navigation-menu'
import { Link } from '@inertiajs/vue3'

defineProps({
    href: String,
    title: String,
    active: Boolean,
})
</script>

<template>
    <li>
        <NavigationMenuLink as-child :active="active">
            <Link :href="href"
                class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground">
            <div class="text-sm font-medium leading-none">{{ title }}</div>
            <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                <slot />
            </p>
            </Link>
        </NavigationMenuLink>
    </li>
</template>