<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <CartIndicator />
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6">
            <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main />
            <div class="lg:hidden mt-4 mb-6">
                <BaseButton :icon="mdiFilterVariant" label="Filtros" color="forest"
                    class="w-full rounded-xl font-semibold" @click="showMobileFilters = !showMobileFilters" />
            </div>
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-6 xl:gap-x-8 mt-6 lg:mt-10">
                <div class="lg:col-span-3 mb-6 lg:mb-0">
                    <div :class="['lg:hidden', showMobileFilters ? 'block' : 'hidden']">
                        <CardBox class="p-4 sm:p-6 mb-6 bg-white dark:bg-slate-800 rounded-2xl shadow-md">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b">
                                <h2
                                    class="text-base sm:text-lg font-bold text-forest-500 dark:text-forest-300 flex items-center gap-2">
                                    <BaseIcon :path="mdiMagnify" class="text-forest-500 w-5 h-5" />
                                    Filtrar Búsqueda
                                </h2>
                                <BaseButton :icon="mdiClose" color="danger" small @click="showMobileFilters = false"
                                    class="lg:hidden" />
                            </div>

                            <FormField label="Categoría" class="mb-4">
                                <FormControl v-model="filters.category_id" type="select" :options="categories"
                                    placeholder="Todas las categorías" @change="applyFilters" />
                            </FormField>

                            <FormField label="Rango de Precios (MXN)" class="mb-4">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <FormControl v-model="filters.min_price" type="number" placeholder="Mín."
                                        class="w-full" @blur="applyFilters" />
                                    <FormControl v-model="filters.max_price" type="number" placeholder="Máx."
                                        class="w-full" @blur="applyFilters" />
                                </div>
                            </FormField>

                            <BaseButton color="" label="Limpiar Filtros" @click="clearFilters"
                                class="w-full mt-4 rounded-xl font-semibold" />
                        </CardBox>
                    </div>
                    <CardBox
                        class="hidden lg:block p-6 sticky top-8 bg-white dark:bg-slate-800 rounded-2xl shadow-md hover:shadow-lg transition">
                        <h2
                            class="text-lg font-bold mb-4 border-b pb-2 text-forest-500 dark:text-forest-300 flex items-center gap-2">
                            <BaseIcon :path="mdiMagnify" class="text-forest-500 w-5 h-5" />
                            Filtrar Búsqueda
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
                        class="bg-white dark:bg-slate-800 p-3 sm:p-4 mb-6 lg:mb-8 rounded-2xl shadow-md border border-gray-100 dark:border-slate-700">
                        <div class="mb-3 sm:mb-4">
                            <FormControl v-model="filters.search" type="text" placeholder="Buscar producto..."
                                @keydown.enter="applyFilters" :icon="mdiMagnify" />
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <label
                                class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Ordenar por:
                            </label>
                            <div class="flex items-center gap-2 flex-1">
                                <FormControl v-model="filters.order" type="select" :options="[
                                    { id: 'name', name: 'Nombre' },
                                    { id: 'price', name: 'Precio' },
                                    { id: 'best_sellers', name: 'Más Vendidos' },
                                    { id: 'stock_availability', name: 'Stock' },
                                ]" @change="applyFilters" class="flex-1" />
                                <BaseButton :icon="filters.direction === 'asc' ? mdiArrowUp : mdiArrowDown"
                                    color="lightDark" small @click="toggleDirection" class="rounded-lg shrink-0"
                                    :title="filters.direction === 'asc' ? 'Ascendente' : 'Descendente'" />
                            </div>
                        </div>
                    </div>
                    <div v-if="products.data.length > 0"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                        <CardBox v-for="product in products.data" :key="product.id"
                            class="overflow-hidden rounded-2xl bg-white dark:bg-slate-800 shadow-md hover:shadow-2xl transition duration-300 flex flex-col border border-gray-100 dark:border-slate-700">
                            <div class="aspect-square w-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                                <img :src="product.photos?.[0]?.path || '/img/No-photo.jpg'" :alt="product.name"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                            </div>
                            <div class="p-4 sm:p-5 flex flex-col flex-grow">
                                <h3
                                    class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight line-clamp-2 min-h-[3rem]">
                                    {{ product.name }}
                                </h3>

                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    Productor: <span class="font-medium">{{ product.producer?.name || 'AgroConecta'
                                    }}</span>
                                </p>

                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4 mt-auto">
                                    <span class="text-xl sm:text-2xl font-extrabold text-earth-600 dark:text-earth-300">
                                        ${{ product.price }}
                                        <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">
                                            /kg o lt
                                        </span>
                                    </span>

                                    <span v-if="product.stock_quantity === 0"
                                        class="px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600 dark:bg-red-800/40 dark:text-red-300 text-center">
                                        Sin Stock
                                    </span>
                                    <span v-else-if="product.stock_quantity <= 5"
                                        class="px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-800/40 dark:text-yellow-300 text-center">
                                        ¡Solo {{ product.stock_quantity }}!
                                    </span>
                                    <span v-else :class="[
                                        'px-2 sm:px-3 py-1 text-xs font-semibold rounded-full text-center',
                                        product.is_available
                                            ? 'bg-green-100 text-green-600 dark:bg-green-800/40 dark:text-green-300'
                                            : 'bg-red-100 text-red-600 dark:bg-red-800/40 dark:text-red-300'
                                    ]">
                                        {{ product.is_available ? 'Disponible' : 'Sin Stock' }}
                                    </span>
                                </div>
                                <BaseButton color="forest" label="Agregar al Carrito"
                                    :disabled="!product.is_available || product.stock_quantity === 0"
                                    class="w-full rounded-lg font-semibold mt-auto text-sm sm:text-base"
                                    :icon="mdiCartPlus" @click="addToCart(product.id)" />
                            </div>
                        </CardBox>
                    </div>
                    <CardBoxComponentEmpty v-else title="No se encontraron productos con esos filtros." />
                    <div class="mt-8 lg:mt-10 flex justify-center">
                        <Pagination :links="products?.meta?.links || []" :total="products?.meta?.total || 0"
                            :to="products?.meta?.to || 0" :from="products?.meta?.from || 0" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import { mdiMagnify, mdiArrowUp, mdiArrowDown, mdiCartPlus, mdiViewModule, mdiFilterVariant, mdiClose } from "@mdi/js";
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

const showMobileFilters = ref(false);

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
            showMobileFilters.value = false;
        }
    });
};
</script>