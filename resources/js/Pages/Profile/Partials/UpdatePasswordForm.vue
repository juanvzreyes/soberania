<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { mdiContentSave } from "@mdi/js";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardSection from "@/Components/CardSection.vue";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <CardBox is-form @submit.prevent="submit">
        <CardSection title="Actualizar Contraseña"
            description="Asegúrate que tu cuenta use una contraseña adecuada y segura.">
            <FormField label="Contraseña Actual" required :error="form.errors.current_password">
                <FormControl ref="currentPasswordInput" v-model="form.current_password" type="password"
                    autocomplete="current-password" required />
            </FormField>

            <FormField label="Nueva Contraseña" required :error="form.errors.password">
                <FormControl ref="passwordInput" v-model="form.password" type="password" autocomplete="new-password"
                    required />
            </FormField>

            <FormField label="Confirmar Contraseña" required :error="form.errors.password_confirmation">
                <FormControl v-model="form.password_confirmation" type="password" autocomplete="new-password"
                    required />
            </FormField>
        </CardSection>

        <template #footer>
            <div class="flex items-center space-x-4">
                <BaseButtons>
                    <BaseButton type="submit" color="contrast" label="Guardar"
                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing" :icon="mdiContentSave" />
                </BaseButtons>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                        Guardado.
                    </p>
                </Transition>
            </div>
        </template>
    </CardBox>
</template>