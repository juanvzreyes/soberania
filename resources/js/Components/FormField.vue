<script setup>
import { computed, useSlots } from "vue";
import BaseIcon from "./BaseIcon.vue";
import { mdiAsterisk, mdiHelpCircleOutline } from "@mdi/js";
import InputError from "./InputError.vue";

defineProps({
  textClass: {
    type: String,
    default: 'text-sm',
  },
  label: {
    type: String,
    default: null,
  },
  labelFor: {
    type: String,
    default: null,
  },
  help: {
    type: String,
    default: null,
  },
  error: {
    type: String,
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const slots = useSlots();

const wrapperClass = computed(() => {
  const base = ['mb-0.5'];
  const slotsLength = slots.default().length;

  if (slotsLength > 1) {
    base.push("grid grid-cols-1 gap-3");
  }

  if (slotsLength === 2) {
    base.push("md:grid-cols-2");
  }

  return base;
});
</script>

<template>
  <div class="mb-6 last:mb-0">
    <label v-if="label" :for="labelFor" class="flex font-bold mb-2 items-center gap-1 dark:text-white" :class="textClass">
      {{ label }}
      <BaseIcon v-show="required" :path="mdiAsterisk" class="text-red-600" w="w-auto" h="h-auto" size="12" />
    </label>
    <div :class="wrapperClass">
      <slot />
    </div>
    <div v-if="help && !error" class="flex items-center text-xs text-gray-500 dark:text-slate-400 mt-1">
      {{ help }}
      <BaseIcon :path="mdiHelpCircleOutline" size="15" />
    </div>
    <InputError :message="error" />
  </div>
</template>
