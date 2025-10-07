<script setup>
import { defineProps } from 'vue';

defineProps({
    path: {
        type: String,
        required: true,
    },
    size: {
        type: [String, Number],
        default: 20,
    },
});
</script>

<template>
    <svg :width="size" :height="size" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path :d="path" />
    </svg>
</template>
