<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <SectionTitleLineWithButton :title="title" main>
            <BarChartBig class="w-8 h-8" />
        </SectionTitleLineWithButton>

        <div class="p-4 mx-auto space-y-8 sm:p-6 lg:p-8">

            <Card>
                <CardHeader>
                    <CardTitle>Reporte de Ventas</CardTitle>
                    <CardDescription>
                        Informe detallado de ventas, pedidos y ganancias por un periodo de tiempo.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <Label for="sales-start-date">Fecha de Inicio</Label>
                        <Input type="date" id="sales-start-date" v-model="salesFilters.startDate" />
                    </div>
                    <div>
                        <Label for="sales-end-date">Fecha de Fin</Label>
                        <Input type="date" id="sales-end-date" v-model="salesFilters.endDate" />
                    </div>
                </CardContent>
                <CardFooter class="flex flex-wrap justify-end gap-2">
                    <Button @click="viewReport('sales')" variant="default">
                        <Search class="w-4 h-4 mr-2" /> Visualizar
                    </Button>
                    <Button as="a" :href="generateReportUrl('sales.excel', salesFilters)" variant="outline">
                        <FileSpreadsheet class="w-4 h-4 mr-2" /> Excel
                    </Button>
                    <Button as="a" :href="generateReportUrl('sales.pdf', salesFilters)" variant="outline">
                        <FileText class="w-4 h-4 mr-2" /> PDF
                    </Button>
                </CardFooter>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Reporte de Inventario</CardTitle>
                    <CardDescription>
                        Análisis de productos con baja disponibilidad y los más vendidos.
                    </CardDescription>
                </CardHeader>
                <CardFooter class="flex flex-wrap justify-end gap-2">
                    <Button @click="viewReport('inventory')" variant="default">
                        <Search class="w-4 h-4 mr-2" /> Visualizar
                    </Button>
                    <Button as="a" :href="generateReportUrl('inventory.excel', {})" variant="outline">
                        <FileSpreadsheet class="w-4 h-4 mr-2" /> Excel
                    </Button>
                    <Button as="a" :href="generateReportUrl('inventory.pdf', {})" variant="outline">
                        <FileText class="w-4 h-4 mr-2" /> PDF
                    </Button>
                </CardFooter>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Reporte de Clientes</CardTitle>
                    <CardDescription>
                        Descubre quiénes son tus clientes más leales y su consumo.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <Label for="customer-start-date">Fecha de Inicio</Label>
                        <Input type="date" id="customer-start-date" v-model="customerFilters.startDate" />
                    </div>
                    <div>
                        <Label for="customer-end-date">Fecha de Fin</Label>
                        <Input type="date" id="customer-end-date" v-model="customerFilters.endDate" />
                    </div>
                </CardContent>
                <CardFooter class="flex flex-wrap justify-end gap-2">
                    <Button @click="viewReport('customers')" variant="default">
                        <Search class="w-4 h-4 mr-2" /> Visualizar
                    </Button>
                    <Button as="a" :href="generateReportUrl('customers.excel', customerFilters)" variant="outline">
                        <FileSpreadsheet class="w-4 h-4 mr-2" /> Excel
                    </Button>
                    <Button as="a" :href="generateReportUrl('customers.pdf', customerFilters)" variant="outline">
                        <FileText class="w-4 h-4 mr-2" /> PDF
                    </Button>
                </CardFooter>
            </Card>
        </div>

        <div v-if="salesReportData || inventoryReportData || customerReportData"
            class="p-4 mx-auto space-y-8 sm:p-6 lg:p-8">

            <div v-if="salesReportData" class="space-y-6">
                <SectionTitleLineWithButton title="Resultados: Reporte de Ventas" main>
                    <BarChartBig class="w-8 h-8" />
                </SectionTitleLineWithButton>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle>Resumen General</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <p class="text-lg"><strong>Total de Ventas:</strong> ${{
                                    Number(salesReportData.totalSales).toFixed(2) }}</p>
                                <p class="text-lg"><strong>Número de Pedidos:</strong> {{ salesReportData.totalOrders }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card v-if="salesReportData.chartUrl">
                        <CardHeader class="pb-2">
                            <CardTitle>Ventas por Día</CardTitle>
                        </CardHeader>
                        <CardContent class="flex items-center justify-center">
                            <img :src="salesReportData.chartUrl" alt="Gráfica de Ventas"
                                class="max-w-full rounded-lg h-auto">
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Detalle de Pedidos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-muted">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Pedido ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Fecha</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Cliente</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Productos</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="!salesReportData.orders || salesReportData.orders.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron
                                            pedidos en el periodo seleccionado.</td>
                                    </tr>
                                    <tr v-else v-for="order in salesReportData.orders" :key="order.id"
                                        class="hover:bg-muted/50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ order.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ new
                                            Date(order.created_at).toLocaleDateString() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ (order.consumer ? order.consumer.name : order.cooperative?.name) || 'N/A'
                                            }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <ul class="list-disc list-inside">
                                                <li v-for="item in (order.orderItems || [])" :key="item.id">
                                                    {{ item.product ? item.product.name : 'N/A' }} ({{ item.quantity }})
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">${{(order.orderItems ||
                                            []).reduce((acc, item) => acc + (item.quantity * item.price), 0).toFixed(2)
                                        }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="inventoryReportData" class="space-y-6">
                <SectionTitleLineWithButton title="Resultados: Reporte de Inventario" main>
                    <Package class="w-8 h-8" />
                </SectionTitleLineWithButton>

                <Card v-if="inventoryReportData.chartUrl">
                    <CardHeader class="pb-2">
                        <CardTitle>Proporción de Productos Vendidos (Top 10)</CardTitle>
                    </CardHeader>
                    <CardContent class="flex items-center justify-center">
                        <img :src="inventoryReportData.chartUrl" alt="Gráfica de Inventario"
                            class="max-w-full rounded-lg h-auto md:max-w-lg">
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Productos con Bajo Stock (10 o menos)</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-muted">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Producto</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Categoría</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr
                                        v-if="!inventoryReportData.lowStockProducts || inventoryReportData.lowStockProducts.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay productos con
                                            bajo stock.</td>
                                    </tr>
                                    <tr v-else v-for="product in inventoryReportData.lowStockProducts" :key="product.id"
                                        class="hover:bg-muted/50 font-medium"
                                        :class="{ 'text-red-600': product.stock_quantity < 5, 'text-yellow-600': product.stock_quantity >= 5 && product.stock_quantity <= 10 }">
                                        <td class="px-6 py-4 text-sm">{{ product.id }}</td>
                                        <td class="px-6 py-4 text-sm">{{ product.name }}</td>
                                        <td class="px-6 py-4 text-sm">{{ product.category ? product.category.name :
                                            'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm">{{ product.stock_quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Productos Más Vendidos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-muted">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Producto</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr
                                        v-if="!inventoryReportData.mostSoldProducts || inventoryReportData.mostSoldProducts.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No se han vendido
                                            productos.</td>
                                    </tr>
                                    <tr v-else v-for="product in inventoryReportData.mostSoldProducts" :key="product.id"
                                        class="hover:bg-muted/50">
                                        <td class="px-6 py-4 text-sm">{{ product.id }}</td>
                                        <td class="px-6 py-4 text-sm font-medium">{{ product.name }}</td>
                                        <td class="px-6 py-4 text-sm font-medium">{{ product.total_sold || 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="customerReportData" class="space-y-6">
                <SectionTitleLineWithButton title="Resultados: Reporte de Clientes" main>
                    <Users class="w-8 h-8" />
                </SectionTitleLineWithButton>

                <Card v-if="customerReportData.chartUrl">
                    <CardHeader class="pb-2">
                        <CardTitle>Top Clientes por Consumo</CardTitle>
                    </CardHeader>
                    <CardContent class="flex items-center justify-center">
                        <img :src="customerReportData.chartUrl" alt="Gráfica de Clientes"
                            class="max-w-full rounded-lg h-auto">
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Detalle de Clientes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-muted">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Nombre</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Email</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                                            Total Gastado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr
                                        v-if="!customerReportData.topCustomers || customerReportData.topCustomers.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No se encontraron
                                            clientes.</td>
                                    </tr>
                                    <tr v-else v-for="customer in customerReportData.topCustomers" :key="customer.id"
                                        class="hover:bg-muted/50">
                                        <td class="px-6 py-4 text-sm">{{ customer.id }}</td>
                                        <td class="px-6 py-4 text-sm font-medium">{{ customer.name }}</td>
                                        <td class="px-6 py-4 text-sm">{{ customer.email }}</td>
                                        <td class="px-6 py-4 text-sm font-medium">${{ Number(customer.total_spent ||
                                            0).toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { BarChartBig, FileSpreadsheet, FileText, Search, Package, Users } from 'lucide-vue-next';

const props = defineProps({
    title: String,
    routeName: String,
    salesReportData: Object,
    inventoryReportData: Object,
    customerReportData: Object,
    filters: {
        type: Object,
        default: () => ({})
    }
});

const salesFilters = ref({
    startDate: props.filters.start_date || '',
    endDate: props.filters.end_date || '',
});

const customerFilters = ref({
    startDate: props.filters.start_date || '',
    endDate: props.filters.end_date || '',
});

const generateReportUrl = (routeNameSuffix, filters) => {
    const baseUrl = route(props.routeName + routeNameSuffix);
    const params = new URLSearchParams();
    if (filters.startDate) params.append('start_date', filters.startDate);
    if (filters.endDate) params.append('end_date', filters.endDate);

    const queryString = params.toString();
    return queryString ? `${baseUrl}?${queryString}` : baseUrl;
};

const viewReport = (reportType) => {
    let filters = { report_type: reportType };

    if (reportType === 'sales') {
        filters.start_date = salesFilters.value.startDate;
        filters.end_date = salesFilters.value.endDate;
    } else if (reportType === 'customers') {
        filters.start_date = customerFilters.value.startDate;
        filters.end_date = customerFilters.value.endDate;
    }

    router.get(route(props.routeName + 'index'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
</script>
