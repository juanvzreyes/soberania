<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    Notificaciones
                </h1>
                <Link v-if="unreadCount > 0" :href="route('notifications.read-all')" method="post" as="button"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                Marcar todas como leídas
                </Link>
            </div>

            <div v-if="notifications.data.length > 0" class="space-y-3">
                <div v-for="notification in notifications.data" :key="notification.id"
                    class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-5 border-l-4 transition hover:shadow-lg"
                    :class="notification.is_read ? 'border-gray-300' : 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center"
                                    :class="notification.is_read ? 'bg-gray-100 dark:bg-slate-700' : 'bg-blue-100 dark:bg-blue-900/40'">
                                    <BaseIcon :path="getIcon(notification.type)" :size="24"
                                        :class="notification.is_read ? 'text-gray-600' : 'text-blue-600'" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                    {{ notification.title }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">
                                    {{ notification.message }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    {{ formatDate(notification.created_at) }}
                                </p>
                            </div>
                        </div>
                        <Link v-if="!notification.is_read" :href="route('notifications.read', notification.id)"
                            method="patch" as="button" class="text-blue-600 hover:underline text-sm ml-4">
                        Marcar como leída
                        </Link>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20">
                <BaseIcon :path="mdiBellOff" :size="64" class="mx-auto text-gray-400 mb-4" />
                <p class="text-xl text-gray-500 dark:text-gray-400">No tienes notificaciones</p>
            </div>

            <div v-if="notifications.data.length > 0" class="mt-6">
                <Pagination :links="notifications.links" :total="notifications.total" :from="notifications.from"
                    :to="notifications.to" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import Pagination from '@/Components/Pagination.vue';
import { mdiBell, mdiBellOff, mdiPackageVariant, mdiTruck, mdiCheckCircle } from '@mdi/js';

defineProps({
    notifications: Object,
    unreadCount: Number,
});

const getIcon = (type) => {
    switch (type) {
        case 'order_confirmation':
            return mdiCheckCircle;
        case 'delivery_pending':
            return mdiTruck;
        default:
            return mdiPackageVariant;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>