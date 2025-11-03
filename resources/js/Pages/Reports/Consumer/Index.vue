<template>
    <section>
        <HeadLogo :title="title" />
        <AuthenticatedLayout>
            <SectionTitleLineWithButton :icon="mdiChartBox" :title="title" main />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <CardBox class="cursor-pointer hover:shadow-lg transition-shadow" @click="openPurchaseHistory">
                    <div class="flex items-start space-x-4">
                        <div class="p-4 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                            <BaseIcon :path="mdiHistory" :size="40" class="text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Historial de Compras
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Visualiza todas tus compras realizadas con detalles de productos, fechas y montos.
                            </p>
                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                <BaseIcon :path="mdiChartLine" :size="16" />
                                <span>Incluye gráfica de líneas</span>
                            </div>
                        </div>
                    </div>
                </CardBox>
                <CardBox class="cursor-pointer hover:shadow-lg transition-shadow" @click="openTopProducts">
                    <div class="flex items-start space-x-4">
                        <div class="p-4 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <BaseIcon :path="mdiTrophy" :size="40" class="text-green-600 dark:text-green-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Productos Más Comprados
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Descubre cuáles son tus productos favoritos con filtros por periodo y categoría.
                            </p>
                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                <BaseIcon :path="mdiChartBar" :size="16" />
                                <span>Incluye gráfica de barras</span>
                            </div>
                        </div>
                    </div>
                </CardBox>
            </div>
            <teleport to="body">
                <div v-if="showPurchaseHistory" class="fixed inset-0 z-50 overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                            @click="closePurchaseHistory"></div>

                        <div
                            class="inline-block w-full max-w-6xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    <BaseIcon :path="mdiHistory" :size="32" class="text-blue-600" />
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        Historial de Compras
                                    </h3>
                                </div>
                                <button @click="closePurchaseHistory" class="text-gray-400 hover:text-gray-600">
                                    <BaseIcon :path="mdiClose" :size="24" />
                                </button>
                            </div>
                            <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Inicio
                                        </label>
                                        <input type="date" v-model="purchaseFilters.start_date"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Fin
                                        </label>
                                        <input type="date" v-model="purchaseFilters.end_date"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div class="flex items-end">
                                        <BaseButton color="" :icon="mdiRefresh" label="Actualizar"
                                            @click="loadPurchaseHistory" class="w-full" />
                                    </div>
                                </div>
                            </div>
                            <div v-if="purchaseData.stats" class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6">
                                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Órdenes</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ purchaseData.stats.total_orders }}
                                    </p>
                                </div>
                                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Productos</p>
                                    <p class="text-2xl font-bold text-green-600">{{ purchaseData.stats.total_products }}
                                    </p>
                                </div>
                                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Gastado</p>
                                    <p class="text-2xl font-bold text-purple-600">${{
                                        formatMoney(purchaseData.stats.total_spent) }}</p>
                                </div>
                                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Promedio</p>
                                    <p class="text-2xl font-bold text-orange-600">${{
                                        formatMoney(purchaseData.stats.average_order) }}</p>
                                </div>
                            </div>
                            <div v-if="purchaseData.chartData && purchaseData.chartData.length > 0" class="p-6">
                                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    Tendencia de Compras
                                </h4>
                                <div class="chart-container">
                                    <LineChart :data="purchaseChartData" :options="lineChartOptions" />
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Orden</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Fecha</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Producto</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Cantidad</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Precio</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="item in purchaseData.purchases"
                                                :key="`${item.order_id}-${item.product_code}`">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{
                                                    item.order_number }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{
                                                    formatDate(item.order_date) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{
                                                    item.product_name }}</td>
                                                <td
                                                    class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                    {{ item.quantity }}</td>
                                                <td
                                                    class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                                    ${{ formatMoney(item.price) }}</td>
                                                <td
                                                    class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                                    ${{ formatMoney(item.subtotal) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div
                                class="flex justify-end space-x-3 p-6 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700">
                                <BaseButton  :icon="mdiFilePdfBox" label="Exportar PDF"
                                    @click="exportReport('purchase-history', 'pdf')" />
                                <BaseButton  :icon="mdiFileExcel" label="Exportar Excel"
                                    @click="exportReport('purchase-history', 'excel')" />
                            </div>
                        </div>
                    </div>
                </div>
            </teleport>
            <teleport to="body">
                <div v-if="showTopProducts" class="fixed inset-0 z-50 overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                            @click="closeTopProducts"></div>

                        <div
                            class="inline-block w-full max-w-6xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    <BaseIcon :path="mdiTrophy" :size="32" class="text-green-600" />
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        Productos Más Comprados
                                    </h3>
                                </div>
                                <button @click="closeTopProducts" class="text-gray-400 hover:text-gray-600">
                                    <BaseIcon :path="mdiClose" :size="24" />
                                </button>
                            </div>

                            <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Inicio
                                        </label>
                                        <input type="date" v-model="topProductsFilters.start_date"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Fin
                                        </label>
                                        <input type="date" v-model="topProductsFilters.end_date"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Categoría
                                        </label>
                                        <select v-model="topProductsFilters.category_id"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            <option :value="null">Todas las categorías</option>
                                            <option v-for="cat in topProductsData.categories" :key="cat.id"
                                                :value="cat.id">
                                                {{ cat.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <BaseButton color="" :icon="mdiRefresh" label="Actualizar"
                                            @click="loadTopProducts" class="w-full" />
                                    </div>
                                </div>
                            </div>

                            <div v-if="topProductsData.topProducts && topProductsData.topProducts.length > 0"
                                class="p-6">
                                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    Cantidad por Producto
                                </h4>
                                <div class="chart-container">
                                    <BarChart :data="topProductsChartData" :options="barChartOptions" />
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Ranking</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Producto</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Categoría</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Cantidad</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Veces Comprado</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                    Total Gastado</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="(product, index) in topProductsData.topProducts"
                                                :key="product.id">
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold"
                                                        :class="getRankingClass(index)">
                                                        {{ index + 1 }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ product.name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{
                                                    product.category_name }}</td>
                                                <td
                                                    class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ product.total_quantity }}</td>
                                                <td
                                                    class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-400">
                                                    {{ product.times_purchased }}</td>
                                                <td class="px-4 py-3 text-sm text-right font-bold text-green-600">${{
                                                    formatMoney(product.total_spent) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div
                                class="flex justify-end space-x-3 p-6 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700">
                                <BaseButton  :icon="mdiFilePdfBox" label="Exportar PDF"
                                    @click="exportReport('top-products', 'pdf')" />
                                <BaseButton  :icon="mdiFileExcel" label="Exportar Excel"
                                    @click="exportReport('top-products', 'excel')" />
                            </div>
                        </div>
                    </div>
                </div>
            </teleport>
        </AuthenticatedLayout>
    </section>
</template>

<script setup>
import { ref, computed } from "vue";
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseIcon from "@/Components/BaseIcon.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import LineChart from "@/Components/Charts/LineChart.vue";
import BarChart from "@/Components/Charts/BarChart.vue";
import {
    mdiChartBox,
    mdiHistory,
    mdiTrophy,
    mdiChartLine,
    mdiChartBar,
    mdiClose,
    mdiRefresh,
    mdiFilePdfBox,
    mdiFileExcel
} from "@mdi/js";
import Swal from "sweetalert2";
import axios from "axios";

const props = defineProps({
    title: { type: String, required: true },
    routeName: { type: String, required: true },
    userName: { type: String, required: true },
});

const showPurchaseHistory = ref(false);
const showTopProducts = ref(false);

const purchaseData = ref({
    purchases: [],
    chartData: [],
    stats: null,
});

const purchaseFilters = ref({
    start_date: new Date(new Date().setMonth(new Date().getMonth() - 6)).toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
});

const topProductsData = ref({
    topProducts: [],
    categories: [],
});

const topProductsFilters = ref({
    start_date: new Date(new Date().setMonth(new Date().getMonth() - 6)).toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
    category_id: null,
    limit: 10,
});

const openPurchaseHistory = async () => {
    showPurchaseHistory.value = true;
    await loadPurchaseHistory();
};

const closePurchaseHistory = () => {
    showPurchaseHistory.value = false;
};

const openTopProducts = async () => {
    showTopProducts.value = true;
    await loadTopProducts();
};

const closeTopProducts = () => {
    showTopProducts.value = false;
};

const loadPurchaseHistory = async () => {
    try {
        const response = await axios.get(route(`${props.routeName}purchase-history`), {
            params: purchaseFilters.value
        });

        if (response.data.success) {
            purchaseData.value = response.data.data;
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Error al cargar el historial de compras',
            confirmButtonColor: '#EF4444'
        });
    }
};

const loadTopProducts = async () => {
    try {
        const response = await axios.get(route(`${props.routeName}top-products`), {
            params: topProductsFilters.value
        });

        if (response.data.success) {
            topProductsData.value = response.data.data;
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Error al cargar los productos',
            confirmButtonColor: '#EF4444'
        });
    }
};

const exportReport = async (type, format) => {
    try {
        Swal.fire({
            title: 'Generando reporte...',
            html: 'Por favor espera mientras se genera el archivo.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let url, params;

        if (type === 'purchase-history') {
            url = route(`${props.routeName}export-purchase-history`);
            params = { ...purchaseFilters.value, format };
        } else {
            url = route(`${props.routeName}export-top-products`);
            params = { ...topProductsFilters.value, format };
        }

        const response = await axios.post(url, params, {
            responseType: 'blob'
        });

        const blob = new Blob([response.data]);
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;

        const extension = format === 'pdf' ? 'pdf' : 'xlsx';
        const filename = type === 'purchase-history'
            ? `historial_compras_${Date.now()}.${extension}`
            : `productos_mas_comprados_${Date.now()}.${extension}`;

        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        link.remove();

        Swal.fire({
            icon: 'success',
            title: '¡Reporte Generado!',
            text: `El archivo se ha descargado exitosamente.`,
            confirmButtonColor: '#10B981'
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Error al generar el reporte',
            confirmButtonColor: '#EF4444'
        });
    }
};

const purchaseChartData = computed(() => {
    if (!purchaseData.value.chartData || purchaseData.value.chartData.length === 0) {
        return { labels: [], datasets: [] };
    }

    return {
        labels: purchaseData.value.chartData.map(item => {
            const [year, month] = item.month.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('es-MX', { month: 'short', year: 'numeric' });
        }),
        datasets: [
            {
                label: 'Total Compras ($)',
                data: purchaseData.value.chartData.map(item => parseFloat(item.total_amount)),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
            }
        ]
    };
});

const topProductsChartData = computed(() => {
    if (!topProductsData.value.topProducts || topProductsData.value.topProducts.length === 0) {
        return { labels: [], datasets: [] };
    }

    return {
        labels: topProductsData.value.topProducts.map(p => p.name),
        datasets: [
            {
                label: 'Cantidad',
                data: topProductsData.value.topProducts.map(p => p.total_quantity),
                backgroundColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6',
                    '#EC4899',
                    '#14B8A6',
                    '#F97316',
                    '#06B6D4',
                    '#84CC16',
                ],
            }
        ]
    };
});

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => '$' + value.toFixed(2)
            }
        }
    }
};

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        }
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
};

const formatMoney = (value) => {
    return parseFloat(value).toFixed(2);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getRankingClass = (index) => {
    if (index === 0) return 'bg-yellow-400 text-yellow-900';
    if (index === 1) return 'bg-gray-300 text-gray-900';
    if (index === 2) return 'bg-orange-400 text-orange-900';
    return 'bg-blue-100 text-blue-900';
};
</script>

<style scoped>
.chart-container {
    height: 300px;
}
</style>