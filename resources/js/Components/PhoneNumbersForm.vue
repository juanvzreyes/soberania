<template>
    <div class="flex flex-col gap-4 md:gap-0 md:flex-row md:items-center md:justify-between mb-6">
        <slot />

        <BaseButton color="" label="Agregar teléfono" @click="addPhone" :icon="mdiPlus" />
    </div>

    <div v-if="phones.length > 0" class="border rounded-lg">
        <table>
            <thead>
                <tr>
                    <th>Teléfono</th>
                    <th>Extensión</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(phone, index) in phones" :key="index">
                    <td data-label="Teléfono">
                        <div class="flex flex-col flex-wrap gap-3">
                            <FormControl v-model="phone.number" type="text" placeholder="7771234567"
                                :icon="mdiNumeric" />
                            <div class="md:max-h-[10px]">
                                <InputError :message="props.errors?.[`phones.${index}.number`]" />
                            </div>
                        </div>
                    </td>
                    <td data-label="Extensión">
                        <div class="flex flex-col flex-wrap gap-3">
                            <FormControl v-model="phone.dial_code" type="text" placeholder="52" :icon="mdiNumeric" />
                            <div class="md:max-h-[10px]">
                                <InputError :message="props.errors?.[`phones.${index}.dial_code`]" />
                            </div>
                        </div>
                    </td>
                    <td data-label="Tipo">
                        <div class="flex flex-col flex-wrap gap-3">
                            <FormControl type="select" v-model="phone.type" valueSelect="value" valueOption="label"
                                :icon="iconFor(phone.type)" :options="phoneTypeOptions" />
                            <div class="md:max-h-[10px]">
                                <InputError :message="props.errors?.[`phones.${index}.type`]" />
                            </div>
                        </div>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex flex-col flex-wrap gap-3">
                            <BaseButtons>
                                <BaseButton @click="removePhone(index)" color="" small :icon="mdiTrashCan" />
                            </BaseButtons>
                            <div class="md:max-h-[10px]"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <CardBoxComponentEmpty v-else />
</template>

<script setup>
import BaseButton from '@/Components/BaseButton.vue';
import { mdiAlphabeticalVariant, mdiCellphone, mdiFax, mdiHomeOutline, mdiNumeric, mdiOfficeBuildingOutline, mdiPlus, mdiTrashCan } from '@mdi/js';
import FormControl from './FormControl.vue';
import InputError from './InputError.vue';
import { usePhoneNumbers } from '@/Hooks/usePhoneNumbers';
import BaseButtons from './BaseButtons.vue';
import CardBoxComponentEmpty from './CardBoxComponentEmpty.vue';

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
});

const phones = defineModel({ default: [] });

const { addPhone, removePhone } = usePhoneNumbers(phones);

const phoneTypeOptions = [
    { value: 'oficina', label: 'Oficina' },
    { value: 'celular', label: 'Celular' },
    { value: 'casa', label: 'Casa' },
    { value: 'fax', label: 'Fax' }
];

const iconFor = (type) => ({
    oficina: mdiOfficeBuildingOutline,
    celular: mdiCellphone,
    casa: mdiHomeOutline,
    fax: mdiFax,
})[type] ?? mdiAlphabeticalVariant;
</script>