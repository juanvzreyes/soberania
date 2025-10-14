<template>
    <CardBox>
        <div class="md:flex md:space-x-4 mb-5">
            <div class="md:w-1/2 max-lg:mb-5">
                <FormField label="Nombre del usuario:" required :error="form.errors.name">
                    <FormControl v-model="form.name" placeholder="Nombre del usuario" />
                </FormField>
            </div>
            <div class="md:w-1/2">
                <FormField label="Correo Electrónico:" required :error="form.errors.email">
                    <FormControl v-model="form.email" type="email" placeholder="Correo Electrónico" />
                </FormField>
            </div>
        </div>
        <FormField :label="isEdit ? 'Nueva contraseña:' : 'Contraseña:'" required :error="form.errors.password">
            <FormControl v-model="form.password" placeholder="Contraseña" type="password" />
        </FormField>
        <FormField label="Selecciona un rol:" required help="Puedes asignarle uno o más roles al usuario">
            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll"
                                class="h-4 w-4 checked:bg-forest-400 ring-forest-100 rounded" />
                        </th>
                        <th>Nombre de Rol</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in roles" :key="item.id">
                        <td>
                            <input type="checkbox" :checked="form.roles.includes(item.id)"
                                @change="() => toggleSelect(item.id)"
                                class="h-4 w-4 checked:bg-forest-400 ring-forest-100 rounded" />
                        </td>
                        <td data-label="Nombre">
                            {{ item.name }}
                        </td>
                        <td data-label="Descripción">
                            {{ item.description }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </FormField>

        <template #footer>
            <BaseButtons>
                <slot name="actions" />
            </BaseButtons>
        </template>
    </CardBox>
</template>
<script setup>
import { defineProps } from 'vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { computed } from "vue";
import CardBox from '@/Components/CardBox.vue';
import BaseButtons from '@/Components/BaseButtons.vue';

const { form, roles } = defineProps({
    form: Object,
    roles: Object,
    isEdit: Boolean
});

const isAllSelected = computed(() => {
    return roles.length > 0 &&
        roles.every(role => form.roles.includes(role.id));
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        form.roles = [];
    } else {
        form.roles = roles.map(role => role.id);
    }
};

const toggleSelect = (id) => {
    if (form.roles.includes(id)) {
        form.roles = form.roles.filter(roleId => roleId !== id);
    } else {
        form.roles.push(id);
    }
};
</script>