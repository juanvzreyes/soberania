<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <SectionTitleLineWithButton :icon="mdiHistory" :title="title" main />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <CardBox
                    class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-2 border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Gastado</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">${{ totalSpent }}</p>
                        </div>
                        <BaseIcon :path="mdiCashMultiple" :size="48" class="text-blue-300 dark:text-blue-700" />
                    </div>
                </CardBox>

                <CardBox
                    class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-2 border-green-200 dark:border-green-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total de Pedidos</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ totalOrders }}</p>
                        </div>
                        <BaseIcon :path="mdiPackageVariant" :size="48" class="text-green-300 dark:text-green-700" />
                    </div>
                </CardBox>
            </div>
            <CardBox class="mb-6 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormField label="Estado">
                        <FormControl v-model="filters.status" type="select" :options="statusOptionsArray"
                            placeholder="Todos los estados" @change="applyFilters" />
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
            <div class="space-y-4">
                <CardBox v-for="order in orders.data" :key="order.id" class="hover:shadow-lg transition">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Pedido #{{ order.order_number }}
                                </h3>
                                <span :class="getStatusClass(order.status)"
                                    class="px-3 py-1 text-xs font-semibold rounded-full">
                                    {{ order.status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ formatDate(order.created_at) }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <img v-for="item in order.order_items.slice(0, 3)" :key="item.id"
                                    :src="getPhotoUrl(item.product.photos)"
                                    class="w-12 h-12 object-cover rounded border-2 border-gray-200 dark:border-gray-600" />
                                <div v-if="order.order_items.length > 3"
                                    class="w-12 h-12 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded border-2 border-gray-200 dark:border-gray-600 text-xs font-bold">
                                    +{{ order.order_items.length - 3 }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <p class="text-2xl font-bold text-forest-600 dark:text-forest-400">
                                ${{ order.total_amount }}
                            </p>
                            <BaseButtons>
                                <BaseButton :href="route('purchase-history.show', order.id)" :icon="mdiEye" small
                                    label="Ver Detalle" />
                                <BaseButton v-if="order.status !== 'Cancelado'" :icon="mdiCart" small
                                    label="Volver a Comprar" @click="reorderAll(order.id)" />
                            </BaseButtons>
                        </div>
                    </div>
                </CardBox>
            </div>

            <div v-if="orders.data.length === 0" class="text-center py-20">
                <BaseIcon :path="mdiPackageVariantClosed" :size="64" class="mx-auto text-gray-400 mb-4" />
                <p class="text-xl text-gray-500 dark:text-gray-400">No tienes compras registradas</p>
                <Link :href="route('catalog.index')" class="text-blue-600 hover:underline mt-2 inline-block">
                Explorar productos
                </Link>
            </div>

            <div v-if="orders.data.length > 0" class="mt-6">
                <Pagination :links="orders.links" :total="orders.total" :from="orders.from" :to="orders.to" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import Pagination from '@/Components/Pagination.vue';
import { mdiHistory, mdiEye, mdiCart, mdiCashMultiple, mdiPackageVariant, mdiPackageVariantClosed } from '@mdi/js';
import Swal from 'sweetalert2';

const props = defineProps({
    orders: Object,
    filters: Object,
    statusOptions: Object,
    totalSpent: String,
    totalOrders: Number,
    title: String,
    routeName: String,
    flash: { type: Object, default: () => ({}) },
});

const filters = reactive({
    status: props.filters.status || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const statusOptionsArray = computed(() => {
    return Object.entries(props.statusOptions).map(([key, value]) => ({
        id: key,
        name: value
    }));
});

const applyFilters = () => {
    router.get(route('purchase-history.index'), filters, {
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

const reorderAll = (orderId) => {
    Swal.fire({
        title: '¿Volver a comprar?',
        text: '¿Deseas agregar todos los productos de este pedido al carrito?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#283C2A',
        confirmButtonText: 'Sí, agregar al carrito',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('purchase-history.reorder', orderId), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Productos agregados',
                        text: 'Los productos han sido agregados al carrito correctamente.',
                        confirmButtonColor: '#10B981'
                    });
                },
                onError: (errors) => {
                    const errorMsg = errors.message || 'Ocurrió un error al agregar los productos.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg,
                        confirmButtonColor: '#E1580E'
                    });
                }
            });
        }
    });
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

onMounted(() => {
    const flash = props.flash || {};
    if (flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: props.flash.success,
            confirmButtonColor: '#10B981'
        });
    } else if (flash.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: props.flash.error,
            confirmButtonColor: '#E1580E'
        });
    } else if (flash.warning) {
        Swal.fire({
            icon: 'warning',
            title: 'Aviso',
            text: props.flash.warning,
            confirmButtonColor: '#F59E0B'
        });
    }
});
</script>