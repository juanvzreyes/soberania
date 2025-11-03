<template>
    <AuthenticatedLayout>
        <section class="min-h-screen bg-gray-50 dark:bg-slate-900 pt-8 sm:pt-12 lg:pt-16 pb-12 sm:pb-16 lg:pb-20">
            <div class="max-w-5xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main />
                <div v-if="cartItems.length > 0"
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-3 sm:p-4 md:p-6 mt-6">
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 dark:border-slate-700">
                                <tr class="text-left text-gray-600 dark:text-gray-400">
                                    <th class="pb-3">Producto</th>
                                    <th class="pb-3">Precio</th>
                                    <th class="pb-3">Cantidad</th>
                                    <th class="pb-3 text-right">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in cartItems" :key="item.id"
                                    class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <img :src="item.photo" class="w-16 h-16 object-cover rounded-lg"
                                                alt="Producto" />
                                            <div>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{
                                                    item.name }}</span>
                                                <div v-if="item.stock_quantity !== undefined && item.stock_quantity < item.quantity"
                                                    class="text-red-600 dark:text-red-400 text-xs mt-1 flex items-center gap-1">
                                                    <span>Solo quedan {{ item.stock_quantity }} unidades
                                                        disponibles</span>
                                                </div>
                                                <div v-else-if="item.stock_quantity !== undefined && item.stock_quantity === 0"
                                                    class="text-red-600 dark:text-red-400 text-xs mt-1 flex items-center gap-1">
                                                    <span>Producto sin stock</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-gray-600 dark:text-gray-300">${{ item.price }}</td>
                                    <td class="text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center gap-2">
                                            <button @click="updateQuantity(item.id, item.quantity - 1)"
                                                class="w-7 h-7 flex items-center justify-center bg-gray-200 dark:bg-slate-700 rounded hover:bg-gray-300 transition">
                                                -
                                            </button>
                                            <span class="w-8 text-center">{{ item.quantity }}</span>
                                            <button @click="updateQuantity(item.id, item.quantity + 1)"
                                                :disabled="item.stock_quantity !== undefined && item.quantity >= item.stock_quantity"
                                                :class="[
                                                    'w-7 h-7 flex items-center justify-center rounded transition',
                                                    item.stock_quantity !== undefined && item.quantity >= item.stock_quantity
                                                        ? 'bg-gray-300 dark:bg-slate-600 cursor-not-allowed opacity-50'
                                                        : 'bg-gray-200 dark:bg-slate-700 hover:bg-gray-300'
                                                ]">
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right font-bold text-forest-600 dark:text-forest-400">
                                        ${{ (item.price * item.quantity).toFixed(2) }}
                                    </td>
                                    <td class="text-right">
                                        <Link :href="route('cart.destroy', item.id)" method="delete" as="button"
                                            class="text-red-600 hover:underline text-sm">
                                        Quitar
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="lg:hidden space-y-4">
                        <div v-for="item in cartItems" :key="item.id"
                            class="border border-gray-200 dark:border-slate-700 rounded-xl p-3 sm:p-4 hover:shadow-md transition">
                            <div class="flex gap-3 mb-3">
                                <img :src="item.photo"
                                    class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg shrink-0" alt="Producto" />
                                <div class="flex-1 min-w-0">
                                    <h3
                                        class="font-semibold text-gray-800 dark:text-gray-200 text-sm sm:text-base mb-1 line-clamp-2">
                                        {{ item.name }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">
                                        ${{ item.price }}
                                    </p>
                                    <div v-if="item.stock_quantity !== undefined && item.stock_quantity < item.quantity"
                                        class="text-red-600 dark:text-red-400 text-xs mt-1">
                                        <span>Solo quedan {{ item.stock_quantity }} unidades</span>
                                    </div>
                                    <div v-else-if="item.stock_quantity !== undefined && item.stock_quantity === 0"
                                        class="text-red-600 dark:text-red-400 text-xs mt-1">
                                        <span>Producto sin stock</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-slate-700">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mr-2">Cantidad:</span>
                                    <button @click="updateQuantity(item.id, item.quantity - 1)"
                                        class="w-8 h-8 flex items-center justify-center bg-gray-200 dark:bg-slate-700 rounded hover:bg-gray-300 transition">
                                        -
                                    </button>
                                    <span class="w-8 text-center font-medium text-sm sm:text-base">{{ item.quantity
                                    }}</span>
                                    <button @click="updateQuantity(item.id, item.quantity + 1)"
                                        :disabled="item.stock_quantity !== undefined && item.quantity >= item.stock_quantity"
                                        :class="[
                                            'w-8 h-8 flex items-center justify-center rounded transition',
                                            item.stock_quantity !== undefined && item.quantity >= item.stock_quantity
                                                ? 'bg-gray-300 dark:bg-slate-600 cursor-not-allowed opacity-50'
                                                : 'bg-gray-200 dark:bg-slate-700 hover:bg-gray-300'
                                        ]">
                                        +
                                    </button>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Subtotal</p>
                                    <p class="font-bold text-forest-600 dark:text-forest-400 text-base sm:text-lg">
                                        ${{ (item.price * item.quantity).toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-slate-700">
                                <Link :href="route('cart.destroy', item.id)" method="delete" as="button"
                                    class="w-full text-center text-red-600 hover:text-red-700 text-sm font-medium py-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                                Quitar del carrito
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 sm:mt-8 space-y-4">
                        <div class="flex justify-end lg:hidden">
                            <div class="bg-gray-50 dark:bg-slate-700/50 px-4 sm:px-6 py-3 rounded-lg">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-1">Subtotal</p>
                                <p class="font-bold text-forest-600 dark:text-forest-400 text-2xl">
                                    ${{ $page.props.cartSubtotal }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-4">
                            <Link :href="route('catalog.index')"
                                class="text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition order-2 sm:order-1">
                            ← Seguir comprando
                            </Link>
                            <div
                                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 order-1 sm:order-2">
                                <div class="flex gap-2 sm:gap-3">
                                    <Link :href="route('cart.clear')" method="delete" as="button"
                                        class="flex-1 sm:flex-none text-center bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                                    Vaciar carrito
                                    </Link>

                                    <BaseButton label="Continuar compra"
                                        class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 !bg-green-600 hover:!bg-green-700 !text-white text-sm font-medium"
                                        @click="$inertia.visit(route('checkout.index'))" />
                                </div>
                                <div class="hidden lg:block bg-gray-50 dark:bg-slate-700/50 px-6 py-3 rounded-lg">
                                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-1">Subtotal</p>
                                    <p class="font-bold text-forest-600 dark:text-forest-400 text-xl">
                                        ${{ $page.props.cartSubtotal }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-12 sm:py-16 lg:py-20 text-gray-500 dark:text-gray-400">
                    <div class="max-w-md mx-auto">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-lg sm:text-xl mb-4 font-medium">Tu carrito está vacío</p>
                        <p class="text-sm sm:text-base text-gray-400 dark:text-gray-500 mb-6">Agrega productos para
                            comenzar tu compra</p>
                        <Link :href="route('catalog.index')"
                            class="inline-block bg-forest-600 hover:bg-forest-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition">
                        Explorar productos
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiViewModule } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";

defineProps({
    title: String,
    cartItems: Array
});

const updateQuantity = (productId, newQuantity) => {
    if (newQuantity < 1) return;

    router.patch(route('cart.update', productId), {
        quantity: newQuantity
    }, {
        preserveScroll: true
    });
};
</script>