<script setup>
import CardBox from './CardBox.vue';
</script>

<template>
    <CardBox hasComponentLayout>
        <div class="px-2 md:px-6 border-b border-gray-200">
            <slot name="header" />
        </div>
        <div class="p-2.5 md:p-6 space-y-6">
            <slot />
        </div>
    </CardBox>
</template>