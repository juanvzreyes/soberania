<template>
    <CardBox>
        <h4 class="text-lg font-semibold mb-6 text-forest-400">Registro de Salida de Producto</h4>
        <div class="md:flex md:space-x-4 mb-5">
            <div class="md:w-1/3 max-lg:mb-5">
                <FormField label="Producto" required :error="form.errors.product_id">
                    <FormControl v-model="form.product_id" type="select" :options="products"
                        placeholder="Seleccione un producto" :error="form.errors.product_id" />
                </FormField>
            </div>

            <div class="md:w-1/3 max-lg:mb-5">
                <FormField label="Stock Total Disponible">
                    <FormControl :model-value="currentStock" type="number" height="h-10" placeholder="0" readonly
                        disabled />
                </FormField>
            </div>

            <div class="md:w-1/3 max-lg:mb-5">
                <FormField label="Cantidad de Salida" required :error="form.errors.quantity">
                    <FormControl v-model="form.quantity" type="number" height="h-10"
                        placeholder="Ej. 5 (Cantidad a restar)" :error="form.errors.quantity" :min="1" />
                </FormField>
            </div>
        </div>

        <div class="md:w-full mb-5">
            <FormField label="Razón de la Salida" required :error="form.errors.reason">
                <FormControl v-model="form.reason" type="textarea"
                    placeholder="Describa la razón para registrar la salida (Ej: Venta local, Merma, Ajuste)"
                    height="h-100" :error="form.errors.reason" />
            </FormField>
        </div>
    </CardBox>
    <CardBox>
        <div class="md:flex justify-between items-center md:space-y-0 space-y-2">
            <BaseButtons>
                <slot />
            </BaseButtons>
        </div>
    </CardBox>
</template>

<script setup>
import { inject, computed } from 'vue';
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButtons from "@/Components/BaseButtons.vue";

const form = inject('form');
const products = inject('products');
const currentStock = inject('currentStock');

</script>