<template>
    <div class="md:flex md:space-x-4 mb-5">
        <div class="md:w-1/2 max-lg:mb-5">
            <FormField label="Nombre de la Categoría" required :error="form.errors.name">
                <FormControl v-model="form.name" placeholder="Ingrese el nombre de la Categoría" />
            </FormField>
        </div>

        <div class="md:w-1/2 max-lg:mb-5">
            <FormField label="Descripción de la Categoría" :error="form.errors.description">
                <FormControl v-model="form.description" type="textarea" placeholder="Ingrese una descripción" />
            </FormField>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});
</script>