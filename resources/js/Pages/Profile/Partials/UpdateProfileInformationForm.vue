<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { mdiContentSave } from "@mdi/js";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardSection from "@/Components/CardSection.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    first_name: user.first_name,
    last_name: user.last_name,
    second_last_name: user.second_last_name,
    gender: user.gender,
    email: user.email,
});

const genderOptions = [
    { id: 'Masculino', name: 'Masculino' },
    { id: 'Femenino', name: 'Femenino' },
    { id: 'Otro', name: 'Otro' },
];

const submit = () => {
    form.patch(route('profile.update'));
};
</script>

<template>
    <CardBox @submit.prevent="submit" is-form>
        <CardSection title="Información de Perfil"
            description="Actualiza tu información de perfil y dirección de correo electrónico.">
            <div class="md:flex md:space-x-4 mb-5">
                <div class="md:w-1/2 max-lg:mb-5">
                    <FormField label="Nombres" required :error="form.errors.first_name">
                        <FormControl v-model="form.first_name" placeholder="Tus nombres" required />
                    </FormField>
                </div>
                <div class="md:w-1/2">
                    <FormField label="Primer Apellido" required :error="form.errors.last_name">
                        <FormControl v-model="form.last_name" placeholder="Tu primer apellido" required />
                    </FormField>
                </div>
            </div>

            <div class="md:flex md:space-x-4 mb-5">
                <div class="md:w-1/2 max-lg:mb-5">
                    <FormField label="Segundo Apellido" :error="form.errors.second_last_name">
                        <FormControl v-model="form.second_last_name" placeholder="Tu segundo apellido" />
                    </FormField>
                </div>
                <div class="md:w-1/2">
                    <FormField label="Género" :error="form.errors.gender">
                        <FormControl v-model="form.gender" type="select" :options="genderOptions"
                            placeholder="Selecciona tu género" />
                    </FormField>
                </div>
            </div>

            <div class="md:w-full mb-5">
                <FormField label="Email" required :error="form.errors.email">
                    <FormControl v-model="form.email" type="email" placeholder="tu@email.com" required />
                </FormField>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Tu email no ha sido verificado.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Click aquí para reenviar el enlace de verificación.
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                    Un nuevo link de verificación ha sido enviado a tu correo.
                </div>
            </div>
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