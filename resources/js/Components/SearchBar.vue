<template>
    <CardBox isForm @submit.prevent="$emit('applyFilters', true)" class="mb-2 p-2">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                <BaseIcon :path="mdiFilterSettings" />
                Filtros de Búsqueda
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ total }} resultados</span>
                <div class="w-2 h-2 bg-success-400 rounded-full animate-pulse"></div>
            </div>
        </div>
        <div class="flex flex-col gap-2 lg:flex-row">
            <div class="flex flex-col sm:flex-row gap-2 md:basis-4/5">
                <FormField class="order-2 sm:order-1" label="Registros">
                    <FormControl @change="$emit('applyFilters', true)" placeholder="Elige un número"
                        :options="rowsPerPage" v-model="rows" :icon="mdiNumeric" />
                </FormField>
                <FormField class="order-2 sm:order-1" v-show="hasPeriod" label="Mostrar desde">
                    <FormControl @change="$emit('applyFilters', true)" type="date" v-model="startDate" />
                </FormField>
                <FormField class="order-2 sm:order-1" v-show="hasPeriod" label="Hasta">
                    <FormControl @change="$emit('applyFilters', true)" type="date" v-model="endDate" />
                </FormField>
                <FormField class="order-1 sm:order-2 sm:grow" label="Búsqueda">
                    <FormControl type="search" ctrlKFocus :icon="mdiMagnify" v-model="search"
                        placeholder="Ingresa un parámetro de búsqueda" />
                </FormField>
            </div>

            <div class="flex gap-2 h-10 sm:justify-center md:basis-1/5 lg:justify-evenly lg:mt-7 bgred">
                <BaseButton @click="$emit('clearFilters')" class="grow" :icon="mdiBroom" color="lightDark"
                    label="Limpiar filtros" small />
                <BaseButton v-if="routeName" class="grow" :routeName="`${routeName}create`" :icon="mdiPlus" color=""
                    label="Agregar" small />
            </div>
        </div>
        <div v-show="withTrashed != null" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <span class="text-gray-900 dark:text-gray-100 font-medium text-sm">Búsquedas rápidas:</span>
                <div class="flex flex-wrap gap-2">
                    <BaseButton small label="Eliminados" :color="withTrashed ? '' : 'lightDark'"
                        @click="withTrashed = !withTrashed" rounded-full />
                </div>
            </div>
        </div>
        <DropdownItem v-show="hasOtherFilters" v-model="showOtherFilters" class="mt-4 lg:mt-0">
            <template #header>Filtros avanzados</template>
            <div class="border rounded-lg p-5 space-y-2">
                <slot />
                <BaseButtons class="justify-center">
                    <BaseButton @click="$emit('applyFilters', true)" :icon="mdiMagnify" color="info"
                        label="Aplicar filtros" />
                </BaseButtons>
            </div>
        </DropdownItem>
    </CardBox>
</template>

<script setup>
import BaseButton from "@/Components/BaseButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormControl from "@/Components/FormControl.vue";
import FormField from "@/Components/FormField.vue";
import DropdownItem from "@/Components/DropdownItem.vue";
import {
    mdiBroom,
    mdiPlus,
    mdiMagnify,
    mdiNumeric,
    mdiFilterSettings
} from "@mdi/js";
import { onMounted, ref, useSlots, watch } from "vue";
import BaseButtons from "./BaseButtons.vue";
import BaseIcon from "./BaseIcon.vue";

const emits = defineEmits(['clearFilters', 'applyFilters']);
const slots = useSlots();

const props = defineProps({
    routeName: {
        type: String,
        default: null,
    },
    total: {
        type: Number,
        default: 0
    },
    hasPeriod: Boolean,
});

const STORAGE_KEY = 'showOtherFilters'
const showOtherFilters = ref(true) // valor por defecto

const search = defineModel('search');
const rows = defineModel('rows');
const startDate = defineModel('startDate');
const endDate = defineModel('endDate');
const withTrashed = defineModel('withTrashed');
const rowsPerPage = ["5", "10", "50"];

const hasOtherFilters = !!slots.default;

watch(withTrashed, () => {
    emits('applyFilters', true);
});

onMounted(() => {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved !== null) {
        showOtherFilters.value = saved === 'true'
    }
});

watch(showOtherFilters, (newValue) => {
    localStorage.setItem(STORAGE_KEY, newValue)
});
</script>