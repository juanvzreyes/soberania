<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

// Estado
const showModal = ref(false);
const timeLeft = ref(0); // Segundos restantes para logout automático
let warningTimer = null;
let logoutTimer = null;
let countdownInterval = null;

// Obtener configuración desde Inertia (minutos a milisegundos)
const page = usePage();
// Convertimos minutos a milisegundos. 
// Ejemplo: Si en .env es 120 min, lifetimeMs = 7200000
const sessionLifetimeMs = (page.props.session?.lifetime || 120) * 60 * 1000;

// Configuración de la alerta (ej: avisar 2 minutos antes de morir)
// Si tu sesión es de 1 min (como en tu env actual), avisamos a los 30 segundos.
const warningTimeMs = sessionLifetimeMs > 120000 ? 120000 : 30000;

const startTimers = () => {
    clearTimers();

    // 1. Temporizador para mostrar la alerta
    warningTimer = setTimeout(() => {
        showModal.value = true;
        startCountdown();
    }, sessionLifetimeMs - warningTimeMs);

    // 2. Temporizador de seguridad para hacer logout si no hace nada
    logoutTimer = setTimeout(() => {
        logout();
    }, sessionLifetimeMs);
};

const startCountdown = () => {
    timeLeft.value = warningTimeMs / 1000;
    countdownInterval = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) clearInterval(countdownInterval);
    }, 1000);
};

const clearTimers = () => {
    clearTimeout(warningTimer);
    clearTimeout(logoutTimer);
    clearInterval(countdownInterval);
};

const extendSession = async () => {
    try {
        // Hacemos ping al servidor para renovar la cookie/sesión DB
        await axios.post(route('session.keep-alive'));

        // Reseteamos la UI
        showModal.value = false;
        startTimers(); // Reinicia el conteo desde cero
    } catch (error) {
        // Si falla (ej. internet caído), forzamos logout
        logout();
    }
};

const logout = () => {
    router.post(route('logout')); // Ajusta a tu ruta de logout
};

// Formato de tiempo visual (MM:SS)
const formattedTime = computed(() => {
    const m = Math.floor(timeLeft.value / 60);
    const s = timeLeft.value % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
});

onMounted(() => {
    if (page.props.auth?.user) {
        startTimers();
    }

    // Opcional: Resetear timer si el usuario navega a otra página (Inertia events)
    router.on('finish', () => {
        if (!showModal.value) startTimers();
    });
});

onUnmounted(() => {
    clearTimers();
});
</script>

<template>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div
            class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                ¿Sigues ahí?
            </h2>

            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Tu sesión expirará en <span class="font-bold text-red-500">{{ formattedTime }}</span>.
                ¿Deseas continuar trabajando?
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="logout"
                    class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700">
                    Cerrar Sesión
                </button>
                <button @click="extendSession"
                    class="px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-900 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                    Continuar Sesión
                </button>
            </div>
        </div>
    </div>
</template>