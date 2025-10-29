<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <Link :href="route('orders.index')" class="text-blue-600 hover:underline flex items-center gap-2">
                <BaseIcon :path="mdiArrowLeft" :size="20" />
                Volver a pedidos
                </Link>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            Pedido #{{ order.id }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Creado el {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    <span :class="getStatusClass(order.status)" class="px-4 py-2 text-sm font-semibold rounded-full">
                        {{ order.status }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <CardBox>
                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                            Información del Cliente
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Nombre:</strong> {{ order.consumer?.name || order.cooperative?.name }}</p>
                            <p><strong>Email:</strong> {{ order.consumer?.email || order.cooperative?.email }}</p>
                        </div>
                    </CardBox>

                    <CardBox class="relative">
                        <Link v-if="order.payment" :href="route('payments.show', order.payment.id)"
                            class="absolute top-4 right-4 text-blue-600 hover:underline text-xs flex items-center gap-1">
                        <BaseIcon :path="mdiOpenInNew" :size="14" />
                        Gestionar
                        </Link>

                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                            Información de Pago
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Método:</strong> {{ order.payment?.payment_method }}</p>
                            <p><strong>Estado:</strong>
                                <span :class="getPaymentStatusClass(order.payment?.status)">
                                    {{ order.payment?.status }}
                                </span>
                            </p>
                            <p><strong>Total:</strong>
                                <span class="text-lg font-bold text-forest-600 dark:text-forest-400">
                                    ${{ order.total_amount }}
                                </span>
                            </p>
                        </div>
                    </CardBox>
                </div>

                <CardBox class="mb-6">
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-gray-100">
                        Productos
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 dark:border-slate-700">
                                <tr class="text-left text-gray-600 dark:text-gray-400">
                                    <th class="pb-3">Producto</th>
                                    <th class="pb-3">Precio Unit.</th>
                                    <th class="pb-3">Cantidad</th>
                                    <th class="pb-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in order.order_items" :key="item.id"
                                    class="border-b border-gray-100 dark:border-slate-700">
                                    <td class="py-3 flex items-center gap-3">
                                        <img :src="getPhotoUrl(item.product.photos)"
                                            class="w-12 h-12 object-cover rounded" />
                                        <span class="font-semibold">{{ item.product.name }}</span>
                                    </td>
                                    <td class="py-3">${{ item.price }}</td>
                                    <td class="py-3">{{ item.quantity }}</td>
                                    <td class="py-3 text-right font-bold text-forest-600 dark:text-forest-400">
                                        ${{ (item.price * item.quantity).toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardBox>
                <CardBox class="mb-6 relative">
                    <Link v-if="order.delivery" :href="route('deliveries.show', order.delivery.id)"
                        class="absolute top-4 right-4 text-blue-600 hover:underline text-xs flex items-center gap-1">
                    <BaseIcon :path="mdiOpenInNew" :size="14" />
                    Gestionar
                    </Link>

                    <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                        Información de Entrega
                    </h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Estado:</strong>
                            <span :class="getDeliveryStatusClass(order.delivery?.status)">
                                {{ order.delivery?.status }}
                            </span>
                        </p>
                        <p><strong>Fecha estimada:</strong>
                            {{ order.delivery?.estimated_delivery_date ?
                                formatDate(order.delivery.estimated_delivery_date) : 'Por confirmar' }}
                        </p>
                        <p v-if="order.delivery?.transporter">
                            <strong>Transportista:</strong> {{ order.delivery.transporter.name }}
                        </p>
                    </div>
                </CardBox>
                <CardBox v-if="canUpdateStatus"
                    class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800">
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-gray-100">
                        Cambiar Estado del Pedido
                    </h3>
                    <div v-if="statusForm.status === 'En preparación' && order.payment?.status !== 'Confirmado'"
                        class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg text-sm">
                        Para cambiar a "En preparación", primero debes
                        <Link :href="route('payments.show', order.payment.id)" class="underline font-semibold">confirmar
                        el pago</Link>.
                    </div>

                    <div v-if="statusForm.status === 'En camino' && order.delivery?.status !== 'En camino'"
                        class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg text-sm">
                        Para cambiar a "En camino", primero debes
                        <Link :href="route('deliveries.show', order.delivery.id)" class="underline font-semibold">marcar
                        la entrega como "En camino"</Link>.
                    </div>

                    <div v-if="statusForm.status === 'Entregado' && order.delivery?.status !== 'Entregado'"
                        class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg text-sm">
                        Para marcar como "Entregado", primero debes
                        <Link :href="route('deliveries.show', order.delivery.id)" class="underline font-semibold">
                        confirmar la entrega</Link>.
                    </div>

                    <form @submit.prevent="updateStatus">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormField label="Nuevo Estado">
                                <FormControl v-model="statusForm.status" type="select" :options="statusOptionsArray"
                                    required />
                            </FormField>

                            <FormField label="Notas (opcional)">
                                <FormControl v-model="statusForm.notes" type="text"
                                    placeholder="Agregar comentarios..." />
                            </FormField>
                        </div>

                        <BaseButtons class="mt-4">
                            <BaseButton type="submit"  label="Actualizar Estado" :icon="mdiCheckCircle"
                                :disabled="processing" />
                        </BaseButtons>
                    </form>
                </CardBox>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardBox from '@/Components/CardBox.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import { mdiArrowLeft, mdiCheckCircle, mdiClose, mdiOpenInNew } from '@mdi/js';

const props = defineProps({
    order: Object,
    canUpdateStatus: Boolean,
    statusOptions: Object,
    title: String,
    routeName: String,
});

const processing = ref(false);

const statusForm = reactive({
    status: props.order.status,
    notes: '',
});

const cancelForm = reactive({
    reason: '',
});

const statusOptionsArray = computed(() => {
    return Object.entries(props.statusOptions).map(([key, value]) => ({
        id: key,
        name: value
    }));
});

const updateStatus = () => {
    processing.value = true;
    router.patch(route('orders.update-status', props.order.id), statusForm, {
        onFinish: () => {
            processing.value = false;
        },
        onSuccess: () => {
            statusForm.notes = '';
        }
    });
};

const cancelOrder = () => {
    if (!confirm('¿Estás seguro de cancelar este pedido? Esta acción no se puede deshacer.')) {
        return;
    }

    processing.value = true;
    router.delete(route('orders.cancel', props.order.id), {
        data: cancelForm,
        onFinish: () => {
            processing.value = false;
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
        if (photos[0].url) {
            return photos[0].url;
        }
        if (photos[0].path) {
            return photos[0].path;
        }
    }
    return '/images/default-product.png';
};
</script>