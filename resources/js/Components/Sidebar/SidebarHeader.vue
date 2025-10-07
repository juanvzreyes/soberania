<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const { isCollapsed } = inject('sidebar');
</script>

<template>
  <div class="flex h-16 shrink-0 items-center justify-center border-b px-6">
    <Link :href="route('dashboard')">
    <ApplicationLogo :class="['h-9 w-auto fill-current text-gray-800 transition-all', isCollapsed && 'h-8 w-8']" />
    </Link>
  </div>
</template>