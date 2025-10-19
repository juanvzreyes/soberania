<template>
    <section>
        <HeadLogo :title="title" />
        <AuthenticatedLayout>
            <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main>
            </SectionTitleLineWithButton>

            <SearchBar @apply-filters="applyFilters" @clear-filters="clearFilters" v-model:search="filters.search"
                v-model:rows="filters.rows" :routeName="routeName" :total="categories?.meta?.total || 0" />
            <CardBox v-if="categories && categories.data && categories.data.length > 0">
                <table>
                    <thead>
                        <tr>
                            <th class="w-2/12">Categoría</th>
                            <th class="w-4/12">Descripción</th>
                            <th class="w-2/12 text-center">Estado</th>
                            <th class="w-3/12">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categories.data" :key="category.id">
                            <td data-label="Categoría" class="font-semibold text-forest-400">
                                {{ category.name }}
                            </td>
                            <td data-label="Descripción">
                                {{ category.description }}
                            </td>
                            <td data-label="Estado" class="text-center">
                                <span
                                    :class="['px-3 py-1 text-xs font-semibold rounded-full', category.is_active ? 'bg-success-100/50 text-success-400' : 'bg-error-100/50 text-error-400']">
                                    {{ category.is_active ? 'Activa' : 'De Baja' }}
                                </span>
                            </td>
                            <td data-label="Acciones" class="before:hidden lg:w-1 whitespace-nowrap">
                                <BaseButtons>
                                    <BaseButton :color="category.is_active ? 'danger' : 'success'"
                                        :icon="category.is_active ? mdiTrashCan : mdiCheck"
                                        @click="toggleCategoryStatus(category)"
                                        :title="category.is_active ? 'Dar de Baja Categoría' : 'Activar Categoría'"
                                        :label="category.is_active ? 'Desactivar' : 'Activar'"
                                        :iconColor="category.is_active ? 'text-red-500' : 'text-green-500'"
                                        :class="category.is_active ? 'text-red-500' : 'text-green-500'" />
                                    <BaseButton color="info" :icon="mdiPencil" :routeName="`${routeName}edit`"
                                        :parameter="category.id" title="Editar Categoría" iconColor="text-blue-600" />
                                </BaseButtons>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pagination :links="categories?.meta?.links || []" :total="categories?.meta?.total || 0"
                    :to="categories?.meta?.to || 0" :from="categories?.meta?.from || 0" />
            </CardBox>
            <CardBoxComponentEmpty v-else />
        </AuthenticatedLayout>
    </section>
</template>

<script setup>
import { defineProps, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiViewModule, mdiPencil, mdiTrashCan,  mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Pagination from "@/Components/Pagination.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import "vue-loading-overlay/dist/css/index.css";
import HeadLogo from "@/Components/HeadLogo.vue";
import { useFilters } from "@/Hooks/useFilters";
import SearchBar from "@/Components/SearchBar.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    title: String,
    categories: Object,
    routeName: String,
    filters: Object,
    flash: Object,
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);

const toggleCategoryStatus = (category) => {
    const isActive = category.is_active;
    const newStatus = !isActive;
    const action = newStatus ? 'Activar' : 'Dar de Baja';
    const color = newStatus ? '#10B981' : '#E1580E';

    window.Swal.fire({
        title: `¿Confirmar ${action}?`,
        text: `Al ${action.toLowerCase()} la categoría "${category.name}", su estado cambiará.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#283C2A',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route(`${props.routeName}update`, category.id), {
                is_active: newStatus
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    window.Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: `Categoría ${newStatus ? 'activada' : 'dada de baja'} correctamente.`
                    });
                },
                onError: (errors) => {
                    const errorMsg = errors.is_active || 'Ocurrió un error al cambiar el estado.';
                    window.Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                }
            });
        }
    });
};
onMounted(() => {
    if (props.flash && props.flash.success) {
        window.Swal.fire({ icon: 'success', title: 'Éxito', text: props.flash.success });
    } else if (props.flash && props.flash.error) {
        window.Swal.fire({ icon: 'error', title: 'Error', text: props.flash.error });
    } else if (props.flash && props.flash.warning) {
        window.Swal.fire({ icon: 'warning', title: 'Aviso', text: props.flash.warning });
    }
});
</script>
