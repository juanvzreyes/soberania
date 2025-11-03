<template>
    <section>
        <HeadLogo :title="title" />
        <AuthenticatedLayout>
            <SectionTitleLineWithButton :icon="mdiChartBox" :title="title" main />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <CardBox class="cursor-pointer hover:shadow-lg transition-shadow" @click="openOrders">
                    <div class="flex items-start space-x-4">
                        <div class="p-4 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <BaseIcon :path="mdiPackageVariant" :size="40" class="text-green-600 dark:text-green-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Pedidos Realizados
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Consulta todos los pedidos recibidos con detalles de productos, cantidades y clientes.
                            </p>
                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                <BaseIcon :path="mdiChartLine" :size="16" />
                                <span>Incluye gráfica de líneas</span>
                            </div>
                        </div>
                    </div>
                </CardBox>
                <CardBox class="cursor-pointer hover:shadow-lg transition-shadow" @click="openOrdersByCategory">
                    <div class="flex items-start space-x-4">
                        <div class="p-4 bg-purple-100 dark:bg-purple-900/40 rounded-lg">
                            <BaseIcon :path="mdiShapeOutline" :size="40" class="text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Pedidos por Categoría
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Analiza la distribución de pedidos según categorías de productos con costos y fechas.
                            </p>
                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                <BaseIcon :path="mdiChartPie" :size="16" />
                                <span>Incluye gráfica de pastel</span>
                            </div>
                        </div>
                    </div>
                </CardBox>
            </div>
            <teleport to="body">
                <div v-if="showOrders" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeOrders"></div>
                        <div class="inline-block w-full max-w-7xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    <BaseIcon :path="mdiPackageVariant" :size="32" class="text-green-600" />
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        Pedidos Realizados
                                    </h3>
                                </div>
                                <button @click="closeOrders" class="text-gray-400 hover:text-gray-600">
                                    <BaseIcon :path="mdiClose" :size="24" />
                                </button>
                            </div>
                            <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Inicio
                                        </label>
                                        <input type="date" v-model="ordersFilters.start_date" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Fin
                                        </label>
                                        <input type="date" v-model="ordersFilters.end_date" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div class="flex items-end">
                                        <BaseButton  :icon="mdiRefresh" label="Actualizar" 
                                            @click="loadOrders" class="w-full" />
                                    </div>
                                </div>
                            </div>
                            <div v-if="ordersData.stats" class="grid grid-cols-2 md:grid-cols-5 gap-4 p-6">
                                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Pedidos</p>
                                    <p class="text-2xl font-bold text-green-600">{{ ordersData.stats.total_orders }}</p>
                                </div>
                                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Productos</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ ordersData.stats.total_products }}</p>
                                </div>
                                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Ingresos</p>
                                    <p class="text-2xl font-bold text-purple-600">${{ formatMoney(ordersData.stats.total_revenue) }}</p>
                                </div>
                                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Pendientes</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ ordersData.stats.pending_orders }}</p>
                                </div>
                                <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Completados</p>
                                    <p class="text-2xl font-bold text-emerald-600">{{ ordersData.stats.completed_orders }}</p>
                                </div>
                            </div>
                            <div v-if="ordersData.chartData && ordersData.chartData.length > 0" class="p-6">
                                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    Tendencia de Pedidos
                                </h4>
                                <div class="chart-container">
                                    <LineChart :data="ordersChartData" :options="lineChartOptions" />
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Orden</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cliente</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Producto</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cantidad</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Precio</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subtotal</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="item in ordersData.orders" :key="`${item.order_id}-${item.product_id}`">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ item.order_number }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(item.order_date) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ item.consumer_name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ item.product_name }}</td>
                                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ item.quantity }}</td>
                                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ formatMoney(item.price) }}</td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">${{ formatMoney(item.subtotal) }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(item.status)">
                                                        {{ item.status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 p-6 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700">
                                <BaseButton  :icon="mdiFilePdfBox" label="Exportar PDF" 
                                    @click="exportReport('orders', 'pdf')" />
                                <BaseButton  :icon="mdiFileExcel" label="Exportar Excel" 
                                    @click="exportReport('orders', 'excel')" />
                            </div>
                        </div>
                    </div>
                </div>
            </teleport>
            <teleport to="body">
                <div v-if="showCategories" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeOrdersByCategory"></div>
                        <div class="inline-block w-full max-w-7xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    <BaseIcon :path="mdiShapeOutline" :size="32" class="text-purple-600" />
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        Pedidos por Categoría
                                    </h3>
                                </div>
                                <button @click="closeOrdersByCategory" class="text-gray-400 hover:text-gray-600">
                                    <BaseIcon :path="mdiClose" :size="24" />
                                </button>
                            </div>
                            <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Inicio
                                        </label>
                                        <input type="date" v-model="categoryFilters.start_date" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Fecha Fin
                                        </label>
                                        <input type="date" v-model="categoryFilters.end_date" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Categoría
                                        </label>
                                        <select v-model="categoryFilters.category_id" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="">Todas las categorías</option>
                                            <option v-for="cat in categoryData.categories" :key="cat.id" :value="cat.id">
                                                {{ cat.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <BaseButton :icon="mdiRefresh" label="Actualizar" 
                                            @click="loadOrdersByCategory" class="w-full" />
                                    </div>
                                </div>
                            </div>
                            <div v-if="categoryData.ordersByCategory && categoryData.ordersByCategory.length > 0" class="p-6">
                                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    Distribución por Categoría
                                </h4>
                                <div class="chart-container" style="height: 400px;">
                                    <PieChart :data="categoryChartData" :options="pieChartOptions" />
                                </div>
                            </div>
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    Resumen por Categoría
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Categoría</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Pedidos</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cantidad Total</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Costo Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="(cat, index) in categoryData.ordersByCategory" :key="cat.category_id"
                                                :class="{ 'bg-green-50 dark:bg-green-900/20': index < 3 }">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ cat.display_category }}
                                                    <span v-if="index === 0" class="ml-2">⭐</span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center text-blue-600 dark:text-blue-400 font-semibold">
                                                    {{ cat.total_orders }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                                    {{ cat.total_quantity }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold text-green-600 dark:text-green-400">
                                                    ${{ formatMoney(cat.total_cost) }}
                                                </td>
                                            </tr>
                                            <tr class="bg-gray-800 dark:bg-gray-900 text-white font-bold">
                                                <td class="px-4 py-3 text-sm">TOTALES</td>
                                                <td class="px-4 py-3 text-sm text-center">{{ totalOrders }}</td>
                                                <td class="px-4 py-3 text-sm text-center">{{ totalQuantity }}</td>
                                                <td class="px-4 py-3 text-sm text-right">${{ formatMoney(totalCost) }}</td>
                                                <td class="px-4 py-3 text-sm text-right">100%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 p-6 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700">
                                <BaseButton  :icon="mdiFilePdfBox" label="Exportar PDF" 
                                    @click="exportReport('categories', 'pdf')" />
                                <BaseButton  :icon="mdiFileExcel" label="Exportar Excel" 
                                    @click="exportReport('categories', 'excel')" />
                            </div>
                        </div>
                    </div>
                </div>
            </teleport>
        </AuthenticatedLayout>
    </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import {
    mdiChartBox,
    mdiPackageVariant,
    mdiShapeOutline,
    mdiChartLine,
    mdiChartPie,
    mdiClose,
    mdiRefresh,
    mdiFilePdfBox,
    mdiFileExcel
} from '@mdi/js'
import HeadLogo from '@/Components/HeadLogo.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import BaseIcon from '@/Components/BaseIcon.vue'
import BaseButton from '@/Components/BaseButton.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import PieChart from '@/Components/Charts/PieChart.vue'

const props = defineProps({
    title: {
        type: String,
        default: 'Reportes de Cooperativa'
    }
})

// Estados para los modales
const showOrders = ref(false)
const showCategories = ref(false)

// Datos para Pedidos
const ordersData = ref({
    orders: [],
    chartData: [],
    stats: null,
    filters: {}
})

const ordersFilters = ref({
    start_date: new Date(Date.now() - 180 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0]
})

// Datos para Categorías
const categoryData = ref({
    ordersByCategory: [],
    orderDetails: [],
    categories: [],
    filters: {}
})

const categoryFilters = ref({
    start_date: new Date(Date.now() - 180 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
    category_id: ''
})

// Funciones para abrir/cerrar modales
const openOrders = () => {
    showOrders.value = true
    loadOrders()
}

const closeOrders = () => {
    showOrders.value = false
}

const openOrdersByCategory = () => {
    showCategories.value = true
    loadOrdersByCategory()
}

const closeOrdersByCategory = () => {
    showCategories.value = false
}

// Cargar datos de pedidos
const loadOrders = async () => {
    try {
        const response = await axios.get('/cooperative/reports/orders', {
            params: ordersFilters.value
        })
        
        if (response.data.success) {
            ordersData.value = response.data.data
        }
    } catch (error) {
        console.error('Error al cargar pedidos:', error)
    }
}

// Cargar datos de categorías
const loadOrdersByCategory = async () => {
    try {
        const response = await axios.get('/cooperative/reports/orders-by-category', {
            params: categoryFilters.value
        })
        
        if (response.data.success) {
            categoryData.value = response.data.data
        }
    } catch (error) {
        console.error('Error al cargar pedidos por categoría:', error)
    }
}

// Exportar reportes
const exportReport = async (type, format) => {
    try {
        let url = ''
        let params = {}
        
        if (type === 'orders') {
            url = '/cooperative/reports/export-orders'
            params = { ...ordersFilters.value, format }
        } else if (type === 'categories') {
            url = '/cooperative/reports/export-orders-by-category'
            params = { ...categoryFilters.value, format }
        }
        
        const response = await axios.get(url, {
            params,
            responseType: 'blob'
        })
        
        // Crear un enlace temporal para descargar
        const blob = new Blob([response.data])
        const link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = `reporte_${type}_${new Date().getTime()}.${format === 'pdf' ? 'pdf' : 'xlsx'}`
        link.click()
    } catch (error) {
        console.error('Error al exportar reporte:', error)
    }
}

// Formateo de datos
const formatMoney = (value) => {
    return new Intl.NumberFormat('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value || 0)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getStatusClass = (status) => {
    const classes = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        'En preparación': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'En camino': 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
        'Entregado': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'Cancelado': 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

// Datos para gráfica de líneas
const ordersChartData = computed(() => {
    if (!ordersData.value.chartData || ordersData.value.chartData.length === 0) {
        return { labels: [], datasets: [] }
    }
    
    const labels = ordersData.value.chartData.map(item => {
        const date = new Date(item.month + '-01')
        return date.toLocaleDateString('es-MX', { year: 'numeric', month: 'short' })
    })
    
    return {
        labels,
        datasets: [{
            label: 'Pedidos',
            data: ordersData.value.chartData.map(item => item.total_orders),
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4
        }]
    }
})

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top'
        }
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
}

// Datos para gráfica de pastel
const categoryChartData = computed(() => {
    if (!categoryData.value.ordersByCategory || categoryData.value.ordersByCategory.length === 0) {
        return { labels: [], datasets: [] }
    }
    
    const colors = [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(245, 158, 11)',
        'rgb(239, 68, 68)',
        'rgb(139, 92, 246)',
        'rgb(236, 72, 153)',
        'rgb(20, 184, 166)',
        'rgb(249, 115, 22)'
    ]
    
    return {
        labels: categoryData.value.ordersByCategory.map(cat => cat.display_category),
        datasets: [{
            label: 'Costo Total',
            data: categoryData.value.ordersByCategory.map(cat => cat.total_cost),
            backgroundColor: colors
        }]
    }
})

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'right'
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    if (label) {
                        label += ': ';
                    }
                    label += '$' + formatMoney(context.parsed);
                    return label;
                }
            }
        }
    }
};


// Cálculos para totales de categorías
const totalOrders = computed(() => {
    return categoryData.value.ordersByCategory.reduce((sum, cat) => sum + cat.total_orders, 0)
})

const totalQuantity = computed(() => {
    return categoryData.value.ordersByCategory.reduce((sum, cat) => sum + cat.total_quantity, 0)
})

const totalCost = computed(() => {
    return categoryData.value.ordersByCategory.reduce((sum, cat) => sum + cat.total_cost, 0)
})

const calculatePercentage = (cost) => {
    if (totalCost.value === 0) return '0.0'
    return ((cost / totalCost.value) * 100).toFixed(1)
}

// Cargar datos iniciales
onMounted(() => {
    // Los datos se cargan al abrir cada modal
})
</script>

