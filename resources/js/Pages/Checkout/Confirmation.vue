<template>
    <AuthenticatedLayout>
        <section class="min-h-screen bg-gray-50 dark:bg-slate-900 pt-16 pb-20">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-8 text-center">
                    <div
                        class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        ¡Pedido realizado con éxito!
                    </h1>

                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Hemos recibido tu pedido #{{ order.id }}
                    </p>

                    <div class="bg-gray-50 dark:bg-slate-700 rounded-lg p-6 mb-6 text-left">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">
                            Resumen del pedido
                        </h3>
                        <div class="space-y-2">
                            <div v-for="item in order.order_items" :key="item.id"
                                class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                                <span>{{ item.product.name }} x{{ item.quantity }}</span>
                                <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                            </div>
                            <div class="border-t border-gray-300 dark:border-gray-600 pt-2 mt-2">
                                <div class="flex justify-between font-bold text-gray-800 dark:text-gray-100">
                                    <span>Total:</span>
                                    <span class="text-forest-600 dark:text-forest-400">${{ order.total_amount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <Link :href="route('catalog.index')"
                            class="px-6 py-2.5 bg-forest-600 hover:bg-forest-700 text-white rounded-lg font-medium transition">
                        Seguir comprando
                        </Link>
                        <Link :href="route('dashboard')" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 
                                   text-gray-700 dark:text-gray-200 rounded-lg font-medium transition">
                        Ir al inicio
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
defineProps({
    order: Object,
});
</script>