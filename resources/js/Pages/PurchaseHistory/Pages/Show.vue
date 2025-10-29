<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <Link :href="route('purchase-history.index')"
                    class="text-blue-600 hover:underline flex items-center gap-2">
                <BaseIcon :path="mdiArrowLeft" :size="20" />
                Volver al historial
                </Link>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            Pedido #{{ order.order_number }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Realizado el {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    <span :class="getStatusClass(order.status)" class="px-4 py-2 text-sm font-semibold rounded-full">
                        {{ order.status }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <CardBox>
                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                            Pago
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Método:</strong> {{ order.payment?.payment_method }}</p>
                            <p><strong>Estado:</strong>
                                <span :class="getPaymentStatusClass(order.payment?.status)">
                                    {{ order.payment?.status }}
                                </span>
                            </p>
                            <p><strong>Total:</strong>
                                <span class="text-xl font-bold text-forest-600 dark:text-forest-400">
                                    ${{ order.total_amount }}
                                </span>
                            </p>
                        </div>
                    </CardBox>

                    <CardBox>
                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                            Entrega
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Estado:</strong>
                                <span :class="getDeliveryStatusClass(order.delivery?.status)">
                                    {{ order.delivery?.status }}
                                </span>
                            </p>
                            <p><strong>Fecha estimada:</strong>
                                {{ order.delivery?.estimated_delivery_date
                                    ? formatDate(order.delivery.estimated_delivery_date)
                                    : 'Por confirmar' }}
                            </p>
                        </div>
                    </CardBox>

                    <CardBox>
                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                            Resumen
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Productos:</strong> {{ order.order_items.length }}</p>
                            <p><strong>Cantidad total:</strong> {{ totalQuantity }}</p>
                        </div>
                    </CardBox>
                </div>
                <CardBox class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                            Productos
                        </h3>
                        <BaseButton v-if="order.status !== 'Cancelado'" color="success" :icon="mdiCart" small
                            label="Volver a Comprar Todo" @click="reorderAll" />
                    </div>

                    <div class="space-y-3">
                        <div v-for="item in order.order_items" :key="item.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-600 transition">
                            <div class="flex items-center gap-4 flex-1">
                                <img :src="getPhotoUrl(item.product.photos)"
                                    class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600" />
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ item.product.name }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Cantidad: {{ item.quantity }} × ${{ item.price }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <p class="text-lg font-bold text-forest-600 dark:text-forest-400">
                                    ${{ (item.quantity * item.price).toFixed(2) }}
                                </p>
                                <BaseButton v-if="order.status !== 'Cancelado' && isProductAvailable(item.product)"
                                    color="info" :icon="mdiCart" small label="Agregar"
                                    @click="reorderProduct(item.product.id)" />
                                <span v-else-if="!isProductAvailable(item.product)"
                                    class="text-xs text-red-600 dark:text-red-400">
                                    No disponible
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-slate-600 mt-4 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total:</span>
                            <span class="text-2xl font-bold text-forest-600 dark:text-forest-400">
                                ${{ order.total_amount }}
                            </span>
                        </div>
                    </div>
                </CardBox>
                <CardBox>
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-gray-100">
                        Línea de Tiempo
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                                <BaseIcon :path="mdiCheckCircle" :size="20"
                                    class="text-green-600 dark:text-green-400" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Pedido realizado</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(order.created_at) }}
                                </p>
                            </div>
                        </div>

                        <div v-if="order.status !== 'Pendiente'" class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                                <BaseIcon :path="mdiPackageVariant" :size="20"
                                    class="text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ order.status }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(order.updated_at) }}
                                </p>
                            </div>
                        </div>

                        <div v-if="order.status === 'Entregado'" class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-full flex items-center justify-center">
                                <BaseIcon :path="mdiTruck" :size="20" class="text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Entregado</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ order.delivery?.estimated_delivery_date
                                        ? formatDate(order.delivery.estimated_delivery_date)
                                        : formatDate(order.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardBox>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import { mdiArrowLeft, mdiCart, mdiCheckCircle, mdiPackageVariant, mdiTruck } from '@mdi/js';

const props = defineProps({
    order: Object,
    title: String,
    routeName: String,
});

const totalQuantity = computed(() => {
    return props.order.order_items.reduce((sum, item) => sum + item.quantity, 0);
});

const reorderAll = () => {
    if (confirm('¿Deseas agregar todos los productos de este pedido al carrito?')) {
        router.post(route('purchase-history.reorder', props.order.id));
    }
};

const reorderProduct = (productId) => {
    router.post(route('purchase-history.reorder', props.order.id), {
        product_ids: [productId]
    });
};

const isProductAvailable = (product) => {
    return product.is_available && product.stock_quantity > 0;
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

const getPaymentStatusClass = (status) => {
    const classes = {
        'Pendiente': 'text-yellow-600 font-semibold',
        'Confirmado': 'text-green-600 font-semibold',
        'Fallido': 'text-red-600 font-semibold',
        'Revertido': 'text-orange-600 font-semibold',
        'Cancelado': 'text-gray-600 font-semibold',
    };
    return classes[status] || 'text-gray-600';
};

const getDeliveryStatusClass = (status) => {
    const classes = {
        'Inicial': 'text-gray-600 font-semibold',
        'En preparación': 'text-blue-600 font-semibold',
        'En camino': 'text-purple-600 font-semibold',
        'Entregado': 'text-green-600 font-semibold',
        'Incidencia': 'text-orange-600 font-semibold',
        'Cancelado': 'text-red-600 font-semibold',
    };
    return classes[status] || 'text-gray-600';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getPhotoUrl = (photos) => {
    if (photos && photos.length > 0) {
        if (photos[0].url) return photos[0].url;
        if (photos[0].path) return photos[0].path;
    }
    return '/images/default-product.png';
};
</script>