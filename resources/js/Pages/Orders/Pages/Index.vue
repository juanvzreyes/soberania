<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <SectionTitleLineWithButton :icon="mdiPackageVariant" :title="title" main />
            <CardBox class="mb-6 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormField label="Estado">
                        <FormControl v-model="filters.status" type="select" :options="statusOptionsArray"
                            placeholder="Todos los estados" @update:modelValue="handleStatusChange" />
                    </FormField>

                    <FormField label="Desde">
                        <FormControl v-model="filters.date_from" type="date" @change="applyFilters" />
                    </FormField>

                    <FormField label="Hasta">
                        <FormControl v-model="filters.date_to" type="date" @change="applyFilters" />
                    </FormField>
                </div>

                <BaseButton label="Limpiar Filtros" color="lightDark" @click="clearFilters" class="mt-4" />
            </CardBox>
            <CardBox class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    #ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Cliente
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Total
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Fecha
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                            <tr v-for="order in orders.data" :key="order.id"
                                class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    #{{ order.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ order.consumer?.name || order.cooperative?.name || 'N/A' }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-forest-600 dark:text-forest-400">
                                    ${{ order.total_amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(order.status)"
                                        class="px-3 py-1 text-xs font-semibold rounded-full">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <BaseButtons>
                                        <BaseButton :href="route('orders.show', order.id)" color="" :icon="mdiEye" small
                                            label="Ver" />
                                    </BaseButtons>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="orders.data.length === 0" class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron pedidos</p>
                </div>

                <div v-if="orders.data.length > 0" class="border-t border-gray-200 dark:border-slate-700 px-4 py-3">
                    <Pagination :links="orders.links" :total="orders.total" :from="orders.from" :to="orders.to" />
                </div>
            </CardBox>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import { mdiPackageVariant, mdiEye } from '@mdi/js';

const props = defineProps({
    orders: Object,
    filters: Object,
    statusOptions: Object,
    title: String,
    routeName: String,
});

const filters = reactive({
    status: props.filters.status || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const statusOptionsArray = computed(() => {
    if (!props.statusOptions) return [];
    return Object.keys(props.statusOptions);
});
const handleStatusChange = (newValue) => {
    if (typeof newValue === 'object' && newValue !== null) {
        filters.status = newValue.value || '';
    } else {
        filters.status = newValue || '';
    }
    applyFilters();
};

const applyFilters = () => {
    router.get(route('orders.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.status = '';
    filters.date_from = '';
    filters.date_to = '';
    applyFilters();
};

const getStatusClass = (status) => {
    const classes = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        'En preparación': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'En camino': 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
        'Entregado': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
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