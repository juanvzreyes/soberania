<template>
    <div class="fixed top-20 right-4 sm:right-6 z-50 transition-all duration-300"
        :class="{ 'opacity-100 translate-y-0': cartCount > 0, 'opacity-0 translate-y-4': cartCount === 0 }"
        v-show="cartCount > 0">
        <a :href="route('cart.index')"
            class="relative inline-flex items-center justify-center p-3 rounded-full shadow-lg bg-white dark:bg-slate-700 text-forest-600 dark:text-forest-300 hover:bg-gray-100 dark:hover:bg-slate-600 transition duration-150 ease-in-out cursor-pointer">
            <BaseIcon :path="mdiCart" class="w-6 h-6" />

            <span v-if="cartCount > 0"
                class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3 h-5 w-5 flex items-center justify-center text-xs font-bold text-white bg-red-600 rounded-full ring-2 ring-white dark:ring-slate-700">
                {{ cartCount < 100 ? cartCount : '99+' }} </span>
        </a>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { mdiCart } from "@mdi/js";
import BaseIcon from "@/Components/BaseIcon.vue";
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const initialCartCount = page.props.cartCount || 0;
const cartCount = ref(initialCartCount);
const updateCartCount = (event) => {
    cartCount.value = event.detail.count;
};

onMounted(() => {
    window.addEventListener('cart-updated', updateCartCount);
});

onUnmounted(() => {
    window.removeEventListener('cart-updated', updateCartCount);
});
</script>