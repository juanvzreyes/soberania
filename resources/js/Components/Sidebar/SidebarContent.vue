<script setup>
import SidebarItem from './SidebarItem.vue';
defineProps({
    menu: Array,
});
</script>

<template>
    <div class="flex-1 overflow-y-auto overflow-x-hidden p-3">
        <div v-for="(group, groupIndex) in menu" :key="groupIndex" class="space-y-1">
            <SidebarItem v-for="(item, itemIndex) in group.items" :key="itemIndex" :item="item" />
        </div>
    </div>
</template>