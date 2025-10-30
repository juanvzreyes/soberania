<script setup lang="ts">
import { computed, watch, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Rocket, AlertCircle, CircleCheck, TriangleAlert } from 'lucide-vue-next';

const page = usePage();
const flash = computed(() => page.props.flash);
const show = ref(false);

const alertConfig = computed(() => {
    if (!flash.value) return null;
    switch (flash.value.type) {
        case 'success':
            return { variant: 'default', title: '¡Éxito!', icon: CircleCheck };
        case 'error':
        case 'danger':
            return { variant: 'destructive', title: 'Error', icon: AlertCircle };
        case 'warning':
            return { variant: 'default', title: 'Advertencia', icon: TriangleAlert };
        default:
            return { variant: 'default', title: 'Atención', icon: Rocket };
    }
});

watch(flash, (newValue) => {
    if (newValue && newValue.message) {
        show.value = true;
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div v-if="show && flash && flash.message && alertConfig">
        <Alert :variant="alertConfig.variant" class="flex items-start gap-3">
            <component :is="alertConfig.icon" class="w-4 h-4 flex-shrink-0" />
            <div>
                <AlertTitle>{{ alertConfig.title }}</AlertTitle>
                <AlertDescription>
                    {{ flash.message }}
                </AlertDescription>
            </div>
        </Alert>
    </div>
</template>
