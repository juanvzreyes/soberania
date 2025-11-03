<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <Link :href="route('deliveries.index')" class="text-blue-600 hover:underline flex items-center gap-2">
                <BaseIcon :path="mdiArrowLeft" :size="20" />
                Volver a entregas
                </Link>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            Entrega #{{ delivery.id }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Pedido #{{ delivery.order_id }}
                        </p>
                    </div>
                    <span :class="getStatusClass(delivery.status)" class="px-4 py-2 text-sm font-semibold rounded-full">
                        {{ delivery.status }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <CardBox>
                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                             Detalles de la Entrega
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Fecha estimada:</strong>
                                {{ delivery.estimated_delivery_date ? formatDate(delivery.estimated_delivery_date) :
                                'Por confirmar' }}
                            </p>
                            <p><strong>Creada:</strong> {{ formatDate(delivery.created_at) }}</p>
                        </div>
                    </CardBox>

                    <CardBox class="relative">
                        <Link :href="route('orders.show', delivery.order.id)"
                            class="absolute top-4 right-4 text-blue-600 hover:underline text-xs flex items-center gap-1">
                        <BaseIcon :path="mdiOpenInNew" :size="14" />
                        Ver pedido
                        </Link>

                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">
                            Información del Pedido
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Cliente:</strong>
                                {{ delivery.order?.consumer?.name || delivery.order?.cooperative?.name }}
                            </p>
                            <p><strong>Estado del pedido:</strong>
                                <span :class="getOrderStatusClass(delivery.order?.status)">
                                    {{ delivery.order?.status }}
                                </span>
                            </p>
                            <p><strong>Estado del pago:</strong>
                                <span :class="getPaymentStatusClass(delivery.order?.payment?.status)">
                                    {{ delivery.order?.payment?.status }}
                                </span>
                            </p>
                        </div>
                    </CardBox>
                </div>
                <CardBox class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800">
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-gray-100">
                        Cambiar Estado de la Entrega
                    </h3>
                    <div v-if="statusForm.status === 'Entregado' && delivery.order?.status !== 'En camino'"
                        class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-lg text-sm">
                        No se puede marcar como entregado. El pedido debe estar "En camino" primero.
                    </div>

                    <form @submit.prevent="updateStatus">
                        <FormField label="Nuevo Estado">
                            <FormControl v-model="statusForm.status" type="select" :options="statusOptionsArray"
                                required />
                        </FormField>

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
import { mdiArrowLeft, mdiCheckCircle, mdiOpenInNew } from '@mdi/js';

const props = defineProps({
    delivery: Object,
    statusOptions: Object,
    title: String,
    routeName: String,
});

const processing = ref(false);

const statusForm = reactive({
    status: props.delivery.status,
});

const statusOptionsArray = computed(() => {
    return Object.entries(props.statusOptions).map(([key, value]) => ({
        id: key,
        name: value
    }));
});

const updateStatus = () => {
    processing.value = true;
    router.patch(route('deliveries.update-status', props.delivery.id), statusForm, {
        onFinish: () => {
            processing.value = false;
        }
    });
};

const getStatusClass = (status) => {
    const classes = {
        'Inicial': 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300',
        'En preparación': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'En camino': 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
        'Entregado': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'Incidencia': 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
        'Cancelado': 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getOrderStatusClass = (status) => {
    const classes = {
        'Pendiente': 'text-yellow-600 font-semibold',
        'En preparación': 'text-blue-600 font-semibold',
        'En camino': 'text-purple-600 font-semibold',
        'Entregado': 'text-green-600 font-semibold',
        'Cancelado': 'text-red-600 font-semibold',
    };
    return classes[status] || 'text-gray-600';
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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>