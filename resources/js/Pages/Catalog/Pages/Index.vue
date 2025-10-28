<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <CartIndicator />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main />
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-8 mt-10">
                <div class="lg:col-span-3 mb-8 lg:mb-0">
                    <CardBox
                        class="p-6 sticky top-8 bg-white dark:bg-slate-800 rounded-2xl shadow-md hover:shadow-lg transition">
                        <h2
                            class="text-lg font-bold mb-4 border-b pb-2 text-forest-500 dark:text-forest-300 flex items-center gap-2">
                            <BaseIcon :path="mdiMagnify" class="text-forest-500 w-5 h-5" /> Filtrar Búsqueda
                        </h2>
                        <FormField label="Categoría" class="mb-5">
                            <FormControl v-model="filters.category_id" type="select" :options="categories"
                                placeholder="Todas las categorías" @change="applyFilters" />
                        </FormField>
                        <FormField label="Rango de Precios (MXN)" class="mb-5">
                            <div class="flex space-x-2">
                                <FormControl v-model="filters.min_price" type="number" placeholder="Min."
                                    @blur="applyFilters" />
                                <FormControl v-model="filters.max_price" type="number" placeholder="Max."
                                    @blur="applyFilters" />
                            </div>
                        </FormField>
                        <BaseButton color="" label="Limpiar Filtros" @click="clearFilters"
                            class="w-full mt-4 rounded-xl font-semibold" />
                    </CardBox>
                </div>
                <div class="lg:col-span-9">
                    <div
                        class="flex flex-wrap items-center justify-between bg-white dark:bg-slate-800 p-4 mb-8 rounded-2xl shadow-md border border-gray-100 dark:border-slate-700">
                        <div class="flex-1 min-w-[200px]">
                            <FormControl v-model="filters.search" type="text"
                                placeholder="Buscar por nombre de producto..." @keydown.enter="applyFilters"
                                :icon="mdiMagnify" />
                        </div>
                        <div class="flex items-center space-x-3 mt-3 md:mt-0 md:ml-6">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordenar por:</label>
                            <FormControl v-model="filters.order" type="select" :options="[
                                { id: 'name', name: 'Nombre' },
                                { id: 'price', name: 'Precio' },
                                { id: 'best_sellers', name: 'Más Vendidos' },
                                { id: 'stock_availability', name: 'Stock' },
                            ]" @change="applyFilters" />
                            <BaseButton :icon="filters.direction === 'asc' ? mdiArrowUp : mdiArrowDown"
                                color="lightDark" small @click="toggleDirection" class="rounded-lg"
                                :title="filters.direction === 'asc' ? 'Ascendente' : 'Descendente'" />
                        </div>
                    </div>
                    <div v-if="products.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <CardBox v-for="product in products.data" :key="product.id"
                            class="overflow-hidden rounded-2xl bg-white dark:bg-slate-800 shadow-md hover:shadow-2xl transition duration-300 flex flex-col border border-gray-100 dark:border-slate-700">
                            <div class="aspect-w-1 aspect-h-1 w-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                                <img :src="product.photos?.[0]?.path || '/img/No-photo.jpg'"
                                    :alt="product.name"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                            </div>
                            <div class="p-5 flex flex-col flex-grow">
                                <h3
                                    class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight line-clamp-2">
                                    {{ product.name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    Productor: <span class="font-medium">{{ product.producer?.name || 'AgroConecta'
                                    }}</span>
                                </p>
                                <div class="flex justify-between items-center mb-4 mt-auto">
                                    <span class="text-2xl font-extrabold text-earth-600 dark:text-earth-300">
                                        ${{ product.price }} <span
                                            class="text-sm font-medium text-gray-600 dark:text-gray-400">/kg o
                                            lt</span>
                                    </span>
                                    <span v-if="product.stock_quantity === 0"
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600 dark:bg-red-800/40 dark:text-red-300">
                                        Sin Stock
                                    </span>
                                    <span v-else-if="product.stock_quantity <= 5"
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-800/40 dark:text-yellow-300">
                                        ¡Solo {{ product.stock_quantity }}!
                                    </span>
                                    <span :class="[
                                        'px-3 py-1 text-xs font-semibold rounded-full',
                                        product.is_available
                                            ? 'bg-green-100 text-green-600 dark:bg-green-800/40 dark:text-green-300'
                                            : 'bg-red-100 text-red-600 dark:bg-red-800/40 dark:text-red-300'
                                    ]">
                                        {{ product.is_available ? 'Disponible' : 'Sin Stock' }}
                                    </span>
                                </div>
                                <BaseButton color="forest" label="Agregar al Carrito"
                                    :disabled="!product.is_available || product.stock_quantity === 0"
                                    class="w-full rounded-lg font-semibold mt-auto" :icon="mdiCartPlus"
                                    @click="addToCart(product.id)" />
                            </div>
                        </CardBox>
                    </div>
                    <CardBoxComponentEmpty v-else title="No se encontraron productos con esos filtros." />
                    <div class="mt-10 flex justify-center">
                        <Pagination :links="products?.meta?.links || []" :total="products?.meta?.total || 0"
                            :to="products?.meta?.to || 0" :from="products?.meta?.from || 0" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import { defineProps } from 'vue';
import { mdiMagnify, mdiArrowUp, mdiArrowDown, mdiCartPlus, mdiViewModule } from "@mdi/js";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import FormControl from "@/Components/FormControl.vue";
import FormField from "@/Components/FormField.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseIcon from "@/Components/BaseIcon.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useCatalog } from "../Composables/useCatalog.js";
import { router } from '@inertiajs/vue3';
import CartIndicator from '@/Components/CartIndicator.vue';

const props = defineProps({
    title: { type: String, required: true },
    products: { type: Object, required: true },
    categories: { type: Array, required: true },
    routeName: { type: String, required: true },
    filters: { type: Object, required: true },
});

const { filters, applyFilters, clearFilters } = useCatalog(props.filters, props.routeName);

const toggleDirection = () => {
    filters.direction = filters.direction === 'asc' ? 'desc' : 'asc';
    applyFilters();
};

const addToCart = (productId) => {
    router.post(route('cart.store', productId), { quantity: 1 }, {
        preserveScroll: true,
        onSuccess: (page) => {
            window.Swal.fire({
                icon: 'success',
                title: 'Añadido al carrito',
                text: 'El producto se agregó correctamente.',
                timer: 1500,
                showConfirmButton: false
            });
            window.dispatchEvent(new CustomEvent('cart-updated', {
                detail: { count: page.props.cartCount }
            }));
        }
    });
};

</script>