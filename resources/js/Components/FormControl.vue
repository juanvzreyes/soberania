<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { useMainStore } from "@/stores/main";
import FormControlIcon from "@/Components/FormControlIcon.vue";
import { mdiAlphabeticalVariant, mdiEye, mdiEyeOff } from "@mdi/js";
import BaseIcon from "./BaseIcon.vue";

const props = defineProps({
  name: {
    type: String,
    default: null,
  },
  height: {
    type: String,
    default: "h-10",
  },
  textClass: {
    type: String,
    default: "text-sm",
  },
  id: {
    type: String,
    default: null,
  },
  autocomplete: {
    type: String,
    default: null,
  },
  maxLength: {
    type: String,
    default: null,
  },
  placeholder: {
    type: String,
    default: 'Seleccione una opción',
  },
  inputmode: {
    type: String,
    default: null,
  },
  icon: {
    type: String,
    default: mdiAlphabeticalVariant,
  },
  options: {
    type: Array,
    default: null,
  },
  type: {
    type: String,
    default: "text",
  },
  modelValue: {
    type: [String, Number, Boolean, Array, Object],
    default: "",
  },
  valueSelect: {
    type: String,
    default: "id",
  },
  valueOption: {
    type: String,
    default: "name",
  },
  selectIsDisabled: {
    type: Boolean,
    default: true,
  },
  min: {
    type: [String, Object],
    default: null,
  },
  max: {
    type: [String, Object],
    default: null,
  },
  roundedFull: Boolean,
  required: Boolean,
  borderless: Boolean,
  transparent: Boolean,
  ctrlKFocus: Boolean,
  disabled: Boolean,
});

const emit = defineEmits(["update:modelValue", "setRef"]);
const mainStore = useMainStore();
const selectEl = ref(null);
const textareaEl = ref(null);
const inputEl = ref(null);
const showPassword = ref([]);
const computedType = computed(() => (props.options ? "select" : props.type));

const computedValue = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit("update:modelValue", value);
  },
});

const inputElClass = computed(() => {
  const base = [
    "w-full max-w-full py-auto",
    "dark:placeholder-gray-400",
    "focus:ring-2 focus:ring-mono-400 focus:border-transparent",
    props.height,
    props.textClass,
    props.type !== 'textarea' && 'transition-all duration-200',
    props.type === 'file' ? 'px-auto' : 'px-2',
    props.borderless ? "border-0" : "border",
    props.disabled
      ? "bg-slate-100 text-slate-500 dark:text-slate-400 dark:bg-transparent border-slate-300 dark:border-slate-700/75 cursor-not-allowed"
      : "bg-white text-black dark:text-slate-200 dark:bg-slate-800 border-slate-400 dark:border-slate-400",
    props.transparent === "bg-transparent",
    props.roundedFull ? "rounded-full" : "rounded",
  ];

  if (props.type === 'file') {
    base.push(
      "file:py-2.5",
      "file:text-sm",
      "file:font-semibold",
      "file:bg-slate-50",
      "file:text-black",
      "file:cursor-pointer",
      "file:w-full"
    );
  }

  if (props.icon && props.type !== 'textarea' && props.type !== 'file') {
    base.push("pl-8");
  }

  if (props.type === 'password') {
    base.push("pr-8");
  }

  return base;
});

const controlIconH = computed(() =>
  props.type === "textarea" ? "h-auto" : "h-full"
);

onMounted(() => {
  if (selectEl.value) {
    emit("setRef", selectEl.value);
  } else if (textareaEl.value) {
    emit("setRef", textareaEl.value);
  } else {
    emit("setRef", inputEl.value);
  }
});

if (props.ctrlKFocus) {
  const fieldFocusHook = (e) => {
    if (e.ctrlKey && e.key === "k") {
      e.preventDefault();
      inputEl.value.focus();
    } else if (e.key === "Escape") {
      inputEl.value.blur();
    }
  };

  onMounted(() => {
    if (!mainStore.isFieldFocusRegistered) {
      window.addEventListener("keydown", fieldFocusHook);
      mainStore.isFieldFocusRegistered = true;
    }
  });

  onBeforeUnmount(() => {
    window.removeEventListener("keydown", fieldFocusHook);
    mainStore.isFieldFocusRegistered = false;
  });
}

defineExpose({
  focus: () => {
    inputEl.value?.focus()
    textareaEl.value?.focus()
    selectEl.value?.focus()
  }
})
</script>

<template>
  <div class="relative">
    <select
      v-if="computedType === 'select'"
      :id="id"
      v-model="computedValue"
      :name="name"
      :class="inputElClass"
      :disabled="disabled"
    >
      <option :value="null" selected :disabled="selectIsDisabled">
        {{ placeholder }}
      </option>
      <option v-for="option in options" :key="option?.id ?? option" :value="option?.[valueSelect] ?? option">
        {{ option?.[valueOption] ?? option }}
      </option>
    </select>

    <textarea
      v-else-if="computedType === 'textarea'"
      :id="id"
      v-model="computedValue"
      :class="inputElClass"
      :name="name"
      :maxLength="maxLength"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
    />
    <input
      v-else
      :id="id"
      ref="inputEl"
      v-model="computedValue"
      :name="name"
      :disabled="disabled"
      :maxLength="maxLength"
      :inputmode="inputmode"
      :autocomplete="autocomplete"
      :required="required"
      :placeholder="placeholder"
      :min="min"
      :max="max"
      :type="showPassword[id] ? 'text' : computedType"
      :class="inputElClass"
    />
    <FormControlIcon 
      v-show="icon && computedType != 'textarea'"  
      :icon="icon" 
      :h="controlIconH" 
    />
    <BaseIcon
      @click="showPassword[id] = !showPassword[id]"
      class="cursor-pointer absolute top-0 right-0 z-10 mr-4 text-gray-800 dark:text-white"
      v-show="type == 'password'"
      :path="showPassword[id] ? mdiEyeOff : mdiEye"
      w="10"
      :h="controlIconH"
    />
    <span
      v-show="type === 'textarea'"
      class="absolute bottom-4 right-3 text-xs text-gray-500 dark:text-gray-400 pointer-events-none">
      {{ modelValue?.length }} / {{ maxLength ?? 255 }}
    </span>
  </div>
</template>
