<template>
    <HeadLogo title="Panel Cooperativa" />

    <AuthenticatedLayout>
        <SectionTitleLineWithButton :icon="mdiAccountGroup" :title="`¡Hola, ${auth.user.name}!`"
            description="Este es el resumen de actividad de tu cooperativa." main :hisBreadCrumb="false" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5 mb-6">
                    <CardBoxWidget trend=" " color="info" :icon="mdiBasketOutline" :number="stats.totalOrders"
                        label="Pedidos de la Cooperativa" tooltip="Total de pedidos históricos" />
                    <CardBoxWidget trend=" " color="success" :icon="mdiPackageVariant"
                        :number="stats.totalProductsPurchased" label="Productos Comprados"
                        tooltip="Total de unidades de producto compradas" />
                    <CardBoxWidget trend=" " color="warning" :icon="mdiCashMultiple" :number="stats.totalSales"
                        prefix="$" label="Monto Total Gastado" tooltip="Monto total histórico de pedidos" />

                </div>

                <CardBox title="Pedidos Recientes de la Cooperativa" :icon="mdiBasketOutline" class="mb-6" has-table>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pedido ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-if="!recentOrders || !recentOrders.length">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Tu cooperativa aún no tiene pedidos.
                                    </td>
                                </tr>
                                <tr v-for="order in recentOrders" :key="order.id"
                                    class="hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-150">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        #{{ order.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{
                                        formatDate(order.created_at) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': order.status === 'Pendiente',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': order.status === 'En preparación',
                                                'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200': order.status === 'En camino',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': order.status === 'Entregado',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': order.status === 'Cancelado'
                                            }">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowVrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                        ${{ order.total_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardBox>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HeadLogo from "@/Components/HeadLogo.vue";
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBoxWidget from '@/Components/CardBoxWidget.vue';
import CardBox from '@/Components/CardBox.vue';
import {
    mdiAccountGroup,
    mdiBasketOutline,
    mdiCashMultiple,
    mdiPackageVariant
} from '@mdi/js';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    auth: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>
