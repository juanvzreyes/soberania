<script setup>
import { inject } from 'vue';
import { cn } from '@/lib/utils';

const { isCollapsed } = inject('sidebar');
</script>

<template>
  <aside :class="cn(
    'fixed inset-y-0 left-0 z-10 hidden h-full flex-col border-r bg-white transition-all duration-300 ease-in-out md:flex',
    isCollapsed ? 'w-20' : 'w-64'
  )">
    <slot />
  </aside>
</template>