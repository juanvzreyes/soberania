<template>
    <CardBox>
        <div class="flex gap-2 items-center">
            <BaseIcon :path="mdiInformationSlabCircleOutline" size="24" h="h-10" w="w-10" />
            <div>
                <h3 class="text-base text-forest-400 font-semibold">Información general</h3>
                <span class="text-sm text-gray-500">
                    Escribe sobre tu empresa e información relevante
                </span>
            </div>
        </div>
        <br>
        <FormField label="Nombre de la cooperativa" :error="props.errors?.name">
            <FormControl v-model="form.name" type="text" placeholder="Ingrese el nombre" />
        </FormField>

        <div class="grid md:grid-cols-2 gap-4">
            <FormField label="Región" help="Ingrese la región en la que se encuentra la cooperativa"
                :error="props.errors?.region">
                <FormControl v-model="form.region" type="text" placeholder="Ingrese el nombre" />
            </FormField>
            <FormField label="Tamaño de la Cooperativa" help="Ingresa en cantidad el tamaño de la cooperativa"
                :error="props.errors?.members">
                <FormControl v-model="form.members" type="number" placeholder="50" />
            </FormField>
        </div>
        <BaseDivider />
        <div class="flex gap-2 items-center">
            <BaseIcon :path="mdiMapMarkerOutline" size="24" h="h-10" w="w-10" />
            <div>
                <h3 class="text-base text-forest-400 font-semibold">Ubicación</h3>
                <span class="text-sm text-gray-500">
                    Define la ubicación física principal
                </span>
            </div>
        </div>
        <br>
        <AddressForm v-model="form.location" :errors="props.errors" />
        <BaseDivider />
        <div>
            <PhoneNumbersForm v-model="form.phones" :errors="props.errors">
                <div class="flex gap-2 items-center">
                    <BaseIcon :path="mdiPhoneOutline" size="24" h="h-10" w="w-10" />
                    <div>
                        <h3 class="text-base text-forest-400 font-semibold">Teléfonos</h3>
                        <span class="text-sm text-gray-500">Administra múltiples números de teléfono con
                            extensiones</span>
                    </div>
                </div>
            </PhoneNumbersForm>
        </div>
        <template #footer>
            <BaseButtons>
                <slot />
            </BaseButtons>
        </template>
    </CardBox>
</template>
<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import AddressForm from '@/Components/AddressForm.vue';
import PhoneNumbersForm from '@/Components/PhoneNumbersForm.vue';
import BaseDivider from '@/Components/BaseDivider.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import { mdiPhoneOutline, mdiMapMarkerOutline, mdiInformationSlabCircleOutline } from '@mdi/js';
import { inject } from 'vue';

const form = inject('form');
const props = inject('props');
</script>