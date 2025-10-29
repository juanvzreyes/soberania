<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <SectionTitleLineWithButton :icon="mdiCashMultiple" :title="title" main />

            <!-- Filtros -->
            <CardBox class="mb-6 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormField label="Estado">
                        <FormControl v-model="filters.status" type="select" :options="statusOptionsArray"
                            placeholder="Todos los estados" @change="applyFilters" />
                    </FormField>

                    <FormField label="Método de Pago">
                        <FormControl v-model="filters.method" type="select" :options="methodOptionsArray"
                            placeholder="Todos los métodos" @change="applyFilters" />
                    </FormField>
                </div>

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
                                    Método</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Monto</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Estado</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                            <tr v-for="payment in payments.data" :key="payment.id"
                                class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    #{{ payment.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    Pedido #{{ payment.order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ payment.order?.consumer?.name || payment.order?.cooperative?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ payment.payment_method }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-forest-600 dark:text-forest-400">
                                    ${{ payment.amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(payment.status)"
                                        class="px-3 py-1 text-xs font-semibold rounded-full">
                                        {{ payment.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <BaseButtons>
                                        <BaseButton :href="route('payments.show', payment.id)" 
                                            :icon="mdiEye" small label="Ver" />
                                    </BaseButtons>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="payments.data.length === 0" class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron pagos</p>
                </div>

                <div v-if="payments.data.length > 0" class="border-t border-gray-200 dark:border-slate-700 px-4 py-3">
                    <Pagination :links="payments.links" :total="payments.total" :from="payments.from"
                        :to="payments.to" />
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
import { mdiCashMultiple, mdiEye } from '@mdi/js';

const props = defineProps({
    payments: Object,
    filters: Object,
    statusOptions: Object,
    methodOptions: Object,
    title: String,
    routeName: String,
});

const filters = reactive({
    status: props.filters.status || '',
    method: props.filters.method || '',
});

const statusOptionsArray = computed(() => {
    return Object.entries(props.statusOptions).map(([key, value]) => ({
        id: key,
        name: value
    }));
});

const methodOptionsArray = computed(() => {
    return Object.entries(props.methodOptions).map(([key, value]) => ({
        id: key,
        name: value
    }));
});

const applyFilters = () => {
    router.get(route('payments.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.status = '';
    filters.method = '';
    applyFilters();
};

const getStatusClass = (status) => {
    const classes = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        'Confirmado': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'Fallido': 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'Revertido': 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
        'Cancelado': 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>