<script setup>
import { inject } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/Components/Icon.vue';
import { mdiMenuOpen, mdiMenuClose } from '@mdi/js';

const { isCollapsed, toggleCollapse } = inject('sidebar');
</script>

<template>
  <Button @click="toggleCollapse" variant="ghost" size="icon" class="shrink-0 hidden md:flex">
    <Icon :path="isCollapsed ? mdiMenuOpen : mdiMenuClose" size="24" />
  </Button>
</template>