<template>
    <AuthenticatedLayout>
        <section class="min-h-screen bg-gray-50 dark:bg-slate-900 pt-16 pb-20">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main />
                <div v-if="cartItems.length > 0" class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6">
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
                                        <img :src="item.photo" class="w-16 h-16 object-cover rounded-lg" />
                                        <div>
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ item.name
                                                }}</span>
                                            <div v-if="item.stock_quantity !== undefined && item.stock_quantity < item.quantity"
                                                class="text-red-600 dark:text-red-400 text-xs mt-1 flex items-center gap-1">
                                                <span>Solo quedan {{ item.stock_quantity }} unidades disponibles</span>
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
                                            class="w-7 h-7 flex items-center justify-center bg-gray-200 dark:bg-slate-700 rounded hover:bg-gray-300">
                                            -
                                        </button>
                                        <span class="w-8 text-center">{{ item.quantity }}</span>
                                        <button @click="updateQuantity(item.id, item.quantity + 1)"
                                            :disabled="item.stock_quantity !== undefined && item.quantity >= item.stock_quantity"
                                            :class="[
                                                'w-7 h-7 flex items-center justify-center rounded',
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
                    <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <Link :href="route('catalog.index')"
                            class="text-forest-600  inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        ← Seguir comprando
                        </Link>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex gap-3">
                                <Link :href="route('cart.clear')" method="delete" as="button"
                                    class="inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                                Vaciar carrito
                                </Link>

                                <BaseButton label="Continuar con la compra"  class="px-5 py-2.5"
                                    @click="$inertia.visit(route('checkout.index'))" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold">
                                Subtotal: <span class="font-bold text-forest-600 dark:text-forest-400 text-xl">
                                    ${{ $page.props.cartSubtotal }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-20 text-gray-500 dark:text-gray-400">
                    <p class="text-xl mb-4">Tu carrito está vacío</p>
                    <Link :href="route('catalog.index')" class="text-forest-600 hover:underline text-sm">Explorar
                    productos</Link>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiMagnify, mdiArrowUp, mdiArrowDown, mdiCartPlus, mdiViewModule } from "@mdi/js";
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
