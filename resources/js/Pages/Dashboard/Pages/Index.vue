<template>
    <HeadLogo title="Dashboard" />
    <AuthenticatedLayout>
        <SectionTitleLineWithButton :icon="mdiMonitorDashboard" title="Panel de Estadísticas"
            description="Panel de control principal" main :hisBreadCrumb="false" />
        <StatisticsPanel :stats="stats" :filters="filters" :auth="auth" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HeadLogo from "@/Components/HeadLogo.vue";
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import { mdiMonitorDashboard } from '@mdi/js';
import StatisticsPanel from '../Components/StatisticsPanel.vue';

const props = defineProps({
    stats: Object,
    filters: Object,
    auth: Object,
});
</script>
