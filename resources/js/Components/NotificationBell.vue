<template>
    <div class="relative">
        <button @click="toggleDropdown"
            class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition">
            <BaseIcon :path="mdiBell" :size="24" />
            <span v-if="unreadCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>
        <div v-if="showDropdown"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 z-50">
            <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Notificaciones</h3>
                <Link v-if="unreadCount > 0" :href="route('notifications.read-all')" method="post" as="button"
                    class="text-xs text-blue-600 hover:underline">
                Marcar todas como leídas
                </Link>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div v-if="loading" class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Cargando...
                </div>

                <div v-else-if="notifications.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                    No tienes notificaciones
                </div>

                <Link v-else v-for="notification in notifications" :key="notification.id"
                    :href="route('notifications.index')"
                    class="block p-4 hover:bg-gray-50 dark:hover:bg-slate-700 border-b border-gray-100 dark:border-slate-700 transition"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.is_read }">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                            <BaseIcon :path="getNotificationIcon(notification.type)" :size="20"
                                class="text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ notification.title }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            {{ notification.message }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            {{ formatDate(notification.created_at) }}
                        </p>
                    </div>
                </div>
                </Link>
            </div>

            <div class="p-3 border-t border-gray-200 dark:border-slate-700">
                <Link :href="route('notifications.index')"
                    class="block text-center text-sm text-blue-600 hover:underline">
                Ver todas las notificaciones
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseIcon from '@/Components/BaseIcon.vue';
import { mdiBell, mdiPackageVariant, mdiTruck, mdiCheckCircle } from '@mdi/js';

const showDropdown = ref(false);
const unreadCount = ref(0);
const notifications = ref([]);
const loading = ref(false);

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value) {
        fetchNotifications();
    }
};

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const response = await fetch(route('notifications.unread-count'));

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        unreadCount.value = data.count || 0;
        notifications.value = data.notifications || [];
    } catch (error) {
        console.error('Error fetching notifications:', error);
        unreadCount.value = 0;
        notifications.value = [];
    } finally {
        loading.value = false;
    }
};

const getNotificationIcon = (type) => {
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
    const d = new Date(date);
    const now = new Date();
    const diffInSeconds = Math.floor((now - d) / 1000);

    if (diffInSeconds < 60) return 'Hace un momento';
    if (diffInSeconds < 3600) return `Hace ${Math.floor(diffInSeconds / 60)} minutos`;
    if (diffInSeconds < 86400) return `Hace ${Math.floor(diffInSeconds / 3600)} horas`;
    return d.toLocaleDateString('es-MX');
};

const handleClickOutside = (event) => {
    const dropdown = event.target.closest('.relative');
    if (!dropdown) {
        showDropdown.value = false;
    }
};

let interval;

onMounted(() => {
    fetchNotifications();
    document.addEventListener('click', handleClickOutside);
    interval = setInterval(fetchNotifications, 30000); // cada 30 segundos
});

onUnmounted(() => {
    if (interval) {
        clearInterval(interval);
    }
    document.removeEventListener('click', handleClickOutside);
});
</script>