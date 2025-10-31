<template>
    <section>
        <HeadLogo :title="title" />
        <AuthenticatedLayout>
            <SectionTitleLineWithButton :icon="mdiViewModule" :title="title" main>
            </SectionTitleLineWithButton>

            <SearchBar @apply-filters="applyFilters" @clear-filters="clearFilters" v-model:search="filters.search"
                v-model:rows="filters.rows" :routeName="routeName" :total="inventoryEntries?.total || 0" />

            <CardBox v-if="inventoryEntries && inventoryEntries.data.length > 0">
                <table>
                    <thead>
                        <tr>
                            <th class="w-3/12">Fecha y Hora</th>
                            <th class="w-3/12">Producto</th>
                            <th class="w-2/12 text-center">Cantidad (Kg / Lts)</th>
                            <th class="w-4/12">Razón del Movimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="entry in inventoryEntries.data" :key="entry.id">
                            <td data-label="Fecha" class="text-sm text-gray-500">
                                {{ new Date(entry.created_at).toLocaleString() }}
                            </td>
                            <td data-label="Producto" class="font-semibold text-forest-400">
                                {{ entry.product.name }}
                            </td>
                            <td data-label="Cantidad" class="text-center font-bold text-success-400">
                                +{{ entry.quantity }}
                            </td>
                            <td data-label="Razón">
                                {{ entry.reason }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pagination :links="inventoryEntries.links" :total="inventoryEntries.total" :to="inventoryEntries.to"
                    :from="inventoryEntries.from" />
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
import { mdiViewModule, mdiPlus } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import Pagination from "@/Components/Pagination.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import SearchBar from "@/Components/SearchBar.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { useFilters } from "@/Hooks/useFilters";

const props = defineProps({
    title: { type: String, required: true },
    inventoryEntries: { type: Object, required: true },
    routeName: { type: String, required: true },
    filters: { type: Object, required: true },
    flash: { type: Object, default: () => ({}) },
});

const { filters, clearFilters, applyFilters } = useFilters(props.filters, props.routeName);

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