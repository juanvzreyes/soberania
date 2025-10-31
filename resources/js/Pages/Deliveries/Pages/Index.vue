<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <SectionTitleLineWithButton :icon="mdiTruck" :title="title" main />

            <!-- Filtros -->
            <CardBox class="mb-6 p-6">
                <FormField label="Estado">
                    <FormControl v-model="filters.status" type="select" :options="statusOptionsArray"
                        placeholder="Todos los estados" @change="applyFilters" />
                </FormField>

                <BaseButton label="Limpiar Filtros" color="lightDark" @click="clearFilters" class="mt-4" />
            </CardBox>

            <!-- Tabla -->
            <CardBox class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    #ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Pedido</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Cliente</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Fecha Estimada</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Estado</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                            <tr v-for="delivery in deliveries.data" :key="delivery.id"
                                class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    #{{ delivery.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    Pedido #{{ delivery.order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ delivery.order?.consumer?.name || delivery.order?.cooperative?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ delivery.estimated_delivery_date ? formatDate(delivery.estimated_delivery_date) :
                                    'Por confirmar' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(delivery.status)"
                                        class="px-3 py-1 text-xs font-semibold rounded-full">
                                        {{ delivery.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <BaseButtons>
                                        <BaseButton :href="route('deliveries.show', delivery.id)" color=""
                                            :icon="mdiEye" small label="Ver" />
                                    </BaseButtons>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="deliveries.data.length === 0" class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron entregas</p>
                </div>

                <div v-if="deliveries.data.length > 0" class="border-t border-gray-200 dark:border-slate-700 px-4 py-3">
                    <Pagination :links="deliveries.links" :total="deliveries.total" :from="deliveries.from"
                        :to="deliveries.to" />
                </div>
            </CardBox>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import { mdiTruck, mdiEye } from '@mdi/js';

const props = defineProps({
    deliveries: Object,
    filters: Object,
    statusOptions: Object,
    title: String,
    routeName: String,
});

const filters = reactive({
    status: props.filters.status || '',
});

const statusOptionsArray = computed(() => {
    return Object.entries(props.statusOptions).map(([key, value]) => ({
        id: key,
        name: value
    }));
});

const applyFilters = () => {
    router.get(route('deliveries.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.status = '';
    applyFilters();
};

const getStatusClass = (status) => {
    const classes = {
        'Inicial': 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300',
        'En preparación': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'En camino': 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
        'Entregado': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'Incidencia': 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
        'Cancelado': 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>