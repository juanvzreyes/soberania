<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <SectionTitleLineWithButton :icon="mdiAccount" :title="title" main />
        <SearchBar @apply-filters="applyFilters" @clear-filters="clearFilters" v-model:search="filters.search"
            v-model:rows="filters.rows" :routeName="routeName"
            :total="users.meta.total" />
        <CardBox v-if="users.data.length > 0">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in users.data" :key="item.id">
                        <td data-label="Nombre">
                            {{ item.name }}
                        </td>
                        <td data-label="Email">
                            {{ item.email }}
                        </td>
                        <td data-label="Rol">
                            <ul class="list-disc list-inside">
                                <li v-for="(role, index) in item.roles" :key="index" class="text-xs">
                                    {{ role.name }}
                                </li>
                            </ul>
                        </td>
                        <td data-label="Acciones">
                            <BaseButtons>
                                <BaseButton color="" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                    :parameter="item.id" title="Editar usuario" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>
        <CardBoxComponentEmpty v-else />
        <pagination :links="users.meta.links" :total="users.meta.total" :to="users.meta.to" :from="users.meta.from" />
    </AuthenticatedLayout>
</template>
<script setup>
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiAccount, mdiPencil } from "@mdi/js";
import SearchBar from "@/Components/SearchBar.vue";
import { useFilters } from "@/Hooks/useFilters";
import CardBox from "@/Components/CardBox.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    users: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        required: true
    },
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);

</script>