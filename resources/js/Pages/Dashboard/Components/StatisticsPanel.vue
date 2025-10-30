<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <CardBox :title="'Filtros y Acciones'" :icon="mdiFilterVariant" class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full sm:w-auto">
                        <div class="w-full">
                            <Label for="start_date" class="dark:text-gray-300 font-medium">Desde:</Label>
                            <Input type="date" v-model="startDate" id="start_date"
                                class="mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700" />
                        </div>
                        <div class="w-full">
                            <Label for="end_date" class="dark:text-gray-300 font-medium">Hasta:</Label>
                            <Input type="date" v-model="endDate" id="end_date"
                                class="mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4 sm:mt-0 w-full sm:w-auto">
                        <Button @click="applyFilters"
                            class="w-full sm:w-auto shadow-lg shadow-blue-500/30 hover:shadow-xl transition-shadow">
                            Filtrar
                        </Button>
                        <Button @click="resetFilters" variant="outline" class="w-full sm:w-auto">
                            Resetear
                        </Button>

                        <TooltipProvider>
                            <template v-if="isAdmin">
                                <TooltipRoot>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="route('dashboard.export.excel', { start_date: startDate, end_date: endDate })">
                                            <Button variant="outline" size="icon" class="w-10 h-10">
                                                <BaseIcon :path="mdiFileExcel" />
                                            </Button>
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Exportar a Excel (.xlsx)</p>
                                    </TooltipContent>
                                </TooltipRoot>

                                <TooltipRoot>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="route('dashboard.export.pdf', { start_date: startDate, end_date: endDate })">
                                            <Button variant="outline" size="icon" class="w-10 h-10">
                                                <BaseIcon :path="mdiFilePdfBox" />
                                            </Button>
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Exportar a PDF</p>
                                    </TooltipContent>
                                </TooltipRoot>
                            </template>
                        </TooltipProvider>
                    </div>

                </div>
            </CardBox>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <CardBoxWidget trend=" " color="info" :icon="isAdmin ? mdiAccountGroup : mdiPackageVariant"
                    :number="stats.primaryStat" :label="isAdmin ? 'Productores Activos' : 'Mis Productos Listados'"
                    :tooltip="isAdmin ? 'Total de productores no eliminados' : 'Total de mis productos activos'"
                    class="hover:scale-[1.02] transition-transform duration-300 ease-out" />

                <CardBoxWidget trend=" " color="success" :icon="mdiCartOutline" :number="stats.totalOrders"
                    :label="`Pedidos (${filters.startDate} a ${filters.endDate})`"
                    :tooltip="isAdmin ? 'Total de pedidos en el rango' : 'Pedidos que incluyen mis productos'"
                    class="hover:scale-[1.02] transition-transform duration-300 ease-out" />
                <CardBoxWidget trend=" " color="warning" :icon="mdiCashMultiple" :number="stats.totalSales" prefix="$"
                    :label="isAdmin ? 'Ventas Globales' : 'Mis Ventas'"
                    :tooltip="isAdmin ? 'Total de ventas en el rango' : 'Total de ventas de mis productos'"
                    class="hover:scale-[1.02] transition-transform duration-300 ease-out" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <CardBox :title="isAdmin ? 'Productos Más Demandados' : 'Mis Productos Más Vendidos'"
                    :icon="mdiChartBar">
                    <div class="h-96 relative">
                        <Bar v-if="stats.topProducts && stats.topProducts.length" :data="chartData"
                            :options="chartOptions" id="productsChart" />
                        <p v-else class="text-center text-gray-500 dark:text-gray-400 pt-16">No hay datos de productos
                            para mostrar.</p>
                    </div>
                </CardBox>

                <CardBox :title="isAdmin ? 'Detalle de Ventas' : 'Detalle de Mis Ventas'" :icon="mdiViewList" has-table>
                    <Table class="dark:text-white">
                        <TableHeader>
                            <TableRow class="border-b-gray-200 dark:border-b-gray-700 hover:bg-transparent">
                                <TableHead class="dark:text-gray-200 font-bold">Producto</TableHead>
                                <TableHead class="dark:text-gray-200 font-bold">Unidades Vendidas</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody class="[&>tr]:border-b [&>tr:last-child]:border-0 dark:[&>tr]:border-gray-700">
                            <template v-if="!stats.topProducts || !stats.topProducts.length">
                                <TableRow class="hover:bg-transparent">
                                    <TableCell :colspan="2" class="text-center text-gray-500 dark:text-gray-400 py-10">
                                        No hay productos para mostrar.
                                    </TableCell>
                                </TableRow>
                            </template>
                            <template v-else>
                                <TableRow v-for="product in stats.topProducts" :key="product.name"
                                    class="dark:hover:bg-gray-800 transition-colors duration-150">
                                    <TableCell class="font-medium">{{ product.name }}</TableCell>
                                    <TableCell>{{ product.total_sold }}</TableCell>
                                </TableRow>
                            </template>
                        </TableBody>
                    </Table>
                </CardBox>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement } from 'chart.js';
import { mdiAccountGroup, mdiCartOutline, mdiCashMultiple, mdiFilterVariant, mdiChartBar, mdiViewList, mdiPackageVariant, mdiFileExcel, mdiFilePdfBox } from '@mdi/js';
import CardBox from '@/Components/CardBox.vue';
import CardBoxWidget from '@/Components/CardBoxWidget.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { TooltipProvider, TooltipTrigger, TooltipContent } from '@/Components/ui/tooltip';
import BaseIcon from '@/Components/BaseIcon.vue';
import { TooltipRoot } from 'reka-ui';
import { useDashboardFilters } from '../Composables/useDashboardFilters';
import { useBarChart } from '../Composables/useBarChart';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement);

const props = defineProps({
    stats: Object,
    filters: Object,
    auth: Object,
});

const isAdmin = computed(() => {
    return props.auth?.roles?.Admin === true;
});

const { startDate, endDate, applyFilters, resetFilters } = useDashboardFilters(props);
const { chartData, chartOptions } = useBarChart(props);

</script>
