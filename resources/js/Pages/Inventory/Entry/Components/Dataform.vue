<template>
    <CardBox>
        <h4 class="text-lg font-semibold mb-6 text-forest-400">Datos del Producto</h4>
        <div class="md:flex md:space-x-4 mb-5">
            <div class="md:w-1/2 max-lg:mb-5">
                <FormField label="Producto" required :error="form.errors.product_id">
                    <FormControl v-model="form.product_id" type="select" :options="products"
                        placeholder="Seleccione un producto" :error="form.errors.product_id" />
                </FormField>
            </div>
            <div class="md:w-1/2 max-lg:mb-5">
                <FormField label="Cantidad de Entrada(Kgs/Lts)" required :error="form.errors.quantity">
                    <FormControl v-model="form.quantity" type="number" height="h-10" placeholder="Ej. 16"
                        :error="form.errors.quantity" />
                </FormField>
            </div>
        </div>

        <div class="md:w-full mb-5">
            <FormField label="Razón de la entrada" required :error="form.errors.reason">
                <FormControl v-model="form.reason" type="textarea"
                    placeholder="Describa la razón para registrar la entrada del producto" height="h-100"
                    :error="form.errors.reason" />
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
import { defineProps, inject } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { mdiPlus, mdiArrowLeft, mdiImageOutline, mdiContentSave, mdiCancel } from "@mdi/js";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import CardSection from "@/Components/CardSection.vue";
import ImageUploadCarousel from "@/Components/ImageUploadCarousel.vue";

const form = inject('form')
const products = inject('products')

</script>
