<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue'
import CardBox from '@/Components/CardBox.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { mdiClose } from '@mdi/js'

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        default: null
    },
    title: {
        type: String,
        default: null
    },
    large: Boolean,
    button: {
        type: String,
        default: 'info'
    },
    hasCancel: Boolean,
})

const emit = defineEmits(['update:modelValue', 'cancel', 'confirm'])
const buttonLabel = computed(() => props.hasCancel ? 'Confirmar' : 'OK')

const value = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const confirmCancel = (mode) => {
    value.value = false
    emit(mode)
}

const confirm = () => confirmCancel('confirm')
const cancel = () => confirmCancel('cancel')

const handleEsc = (e) => {
    if (e.key === 'Escape' && value.value) {
        cancel()
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleEsc)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleEsc)
})
</script>

<template>
    <Transition enter-active-class="transition-opacity duration-300 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-show="value"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden bg-gray-900 bg-opacity-60">
            <Transition enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                <CardBox v-show="value" class="shadow-lg w-full max-h-modal md:w-3/5 lg:w-2/5 z-50" is-modal
                    @submit.prevent="confirm">
                    <header class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                        <h4 class="text-xl font-bold">
                            {{ title }}
                        </h4>
                        <BaseButton :icon="mdiClose" color="whiteDark" small rounded-full @click.prevent="cancel" />
                    </header>

                    <div class="p-6">
                        <slot />
                    </div>

                    <template #footer>
                        <slot name="footer">
                            <BaseButtons>
                                <BaseButton :label="buttonLabel" :color="button" @click="confirm" />
                                <BaseButton v-if="hasCancel" label="Cancelar" :color="button" outline @click="cancel" />
                            </BaseButtons>
                        </slot>
                    </template>
                </CardBox>
            </Transition>
        </div>
    </Transition>
</template>