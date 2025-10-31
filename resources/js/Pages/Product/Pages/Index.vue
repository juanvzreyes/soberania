<template>
    <section>
        <HeadLogo :title="title" />
        <AuthenticatedLayout>
            <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main />

            <SearchBar @apply-filters="applyFilters" @clear-filters="clearFilters" v-model:search="filters.search"
                v-model:rows="filters.rows" :routeName="routeName" :total="products?.meta?.total || 0" />

            <CardBox v-if="products && products.data && products.data.length > 0">
                <table>
                    <thead>
                        <tr>
                            <th class="w-2/12">Nombre</th>
                            <th class="w-3/12">Descripción</th>
                            <th class="w-2/12">Precio</th>
                            <th class="w-2/12 text-center">Estado</th>
                            <th class="w-3/12">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id">
                            <td data-label="Nombre" class="font-semibold text-forest-400">
                                {{ product.name }}
                            </td>
                            <td data-label="Descripción">
                                {{ product.description }}
                            </td>
                            <td data-label="Precio">
                                ${{ product.price }}
                            </td>
                            <td data-label="Estado" class="text-center">
                                <span :class="[
                                    'px-3 py-1 text-xs font-semibold rounded-full',
                                    product.is_available ? 'bg-success-100/50 text-success-400' : 'bg-error-100/50 text-error-400'
                                ]">
                                    {{ product.is_available ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td data-label="Acciones" class="before:hidden lg:w-1 whitespace-nowrap">
                                <BaseButtons>
                                    <BaseButton :color="product.is_available ? '' : ''"
                                        :icon="product.is_available ? mdiTrashCan : mdiCheck"
                                        @click="toggleProductStatus(product)"
                                        :title="product.is_available ? 'Dar de Baja Producto' : 'Activar Producto'"
                                        :label="product.is_available ? 'Desactivar' : 'Activar'" />
                                    <BaseButton color="" :icon="mdiPencil" :routeName="`${routeName}edit`"
                                        :parameter="product.id" title="Editar Producto" />
                                </BaseButtons>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <Pagination :links="products?.meta?.links || []" :total="products?.meta?.total || 0"
                    :to="products?.meta?.to || 0" :from="products?.meta?.from || 0" />
            </CardBox>

            <CardBoxComponentEmpty v-else />
        </AuthenticatedLayout>
    </section>
</template>

<script setup>
import { defineProps, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiViewModule,
    mdiPencil,
    mdiTrashCan,
    mdiCheck,
} from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Pagination from "@/Components/Pagination.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import SearchBar from "@/Components/SearchBar.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { useFilters } from "@/Hooks/useFilters";

const props = defineProps({
    title: { type: String, required: true },
    products: { type: Object, required: true },
    routeName: { type: String, required: true },
    filters: { type: Object, required: true },
    flash: { type: Object, default: () => ({}) },
});

const { filters, clearFilters, applyFilters } = useFilters(props.filters, props.routeName);

const toggleProductStatus = (product) => {
    const isAvailable = product.is_available;
    const newStatus = !isAvailable;
    const action = newStatus ? 'Activar' : 'Dar de Baja';
    const color = newStatus ? '#10B981' : '#E1580E';

    Swal.fire({
        title: `¿Confirmar ${action}?`,
        text: `Al ${action.toLowerCase()} el producto "${product.name}", su estado cambiará.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#283C2A',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route(`${props.routeName}update`, product.id), {
                is_available: newStatus
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: `Producto ${newStatus ? 'activado' : 'dado de baja'} correctamente.`
                    });
                },
                onError: (errors) => {
                    const errorMsg = errors.is_available || 'Ocurrió un error al cambiar el estado.';
                    Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                }
            });
        }
    });
};


onMounted(() => {
    const flash = props.flash || {};
    if (flash.success) {
        Swal.fire({ icon: "success", title: "Éxito", text: props.flash.success });
    } else if (flash.error) {
        Swal.fire({ icon: "error", title: "Error", text: props.flash.error });
    } else if (flash.warning) {
        Swal.fire({ icon: "warning", title: "Aviso", text: props.flash.warning });
    }
});

</script>
