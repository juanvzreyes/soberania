<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout :title="title">
    </AuthenticatedLayout>
</template>
<script setup>
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
});

</script>