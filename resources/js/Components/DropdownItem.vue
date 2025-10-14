<script setup>
import { ref, watch } from 'vue';
import BaseIcon from "@/Components/BaseIcon.vue";
import {
    mdiChevronDown,
    mdiChevronUp
} from "@mdi/js";
import BaseDivider from './BaseDivider.vue';

const props = defineProps({
    modelValue: { type: Boolean, default: true }
})

const emit = defineEmits(['update:modelValue'])
const open = ref(props.modelValue)

watch(() => props.modelValue, (val) => {
    open.value = val
})

function toggle() {
    open.value = !open.value
    emit('update:modelValue', open.value)
}
</script>
<template>
    <div class="lg:w-full text-[.85rem] lg:ml-0">
        <h3 @click="toggle"
            class="text-sm font-medium focus:outline-none focus:underline transition-colors duration-200 cursor-pointer flex items-center justify-between hover:textsla"
            :class="open ? 'text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400'">
            <div class="rounded-lg items-center flex gap-0.5">
                <slot name="header" />
                <BaseIcon :path="open ? mdiChevronDown : mdiChevronUp" size="22" />
            </div>
        </h3>
        <div v-if="open"
            class="lg:mr-0 lg:text-left text-dark-grayish-blue my-3.5 animate-fade-down animate-once animate-duration-300">
            <slot />
            <BaseDivider />
        </div>
    </div>
</template>