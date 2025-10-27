<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { mdiTrashCan } from "@mdi/js";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardSection from "@/Components/CardSection.vue";
import CardBoxModal from "@/Components/CardBoxModal.vue";

const confirmingUserDeletion = ref(false);

const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div>
        <CardBox>
            <CardSection title="Eliminar Cuenta"
                description="Una vez tu cuenta es eliminada, todos los recursos y datos serán permanentemente eliminados. Antes de eliminar tu cuenta, por favor descarga cualquier información o datos que desees conservar.">
            </CardSection>

            <template #footer>
                <BaseButtons>
                    <BaseButton color="" label="Eliminar Cuenta" :icon="mdiTrashCan"
                        @click="confirmUserDeletion" />
                </BaseButtons>
            </template>
        </CardBox>

        <CardBoxModal v-model="confirmingUserDeletion" title="¿Estás seguro de que deseas eliminar tu cuenta?"
            button="" has-cancel @confirm="deleteUser">
            <p class="mb-6">
                Una vez tu cuenta es eliminada, todos sus recursos y datos serán permanentemente eliminados. Por favor
                ingresa tu contraseña para confirmar que deseas eliminar tu cuenta permanentemente.
            </p>

            <FormField label="Contraseña" :error="form.errors.password">
                <FormControl ref="passwordInput" v-model="form.password" type="password"
                    placeholder="Ingresa tu contraseña" @keyup.enter="deleteUser" />
            </FormField>

            <template #footer>
                <BaseButtons>
                    <BaseButton label="Cancelar" color="info" outline @click="closeModal" />
                    <BaseButton label="Eliminar Cuenta" color="" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing" @click="deleteUser" />
                </BaseButtons>
            </template>
        </CardBoxModal>
    </div>
</template>