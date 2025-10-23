<template>
    <CardBox>
        <h4 class="text-lg font-semibold mb-6 text-forest-400">Datos del Producto</h4>
        <div class="md:flex md:space-x-4 mb-5">
            <div class="md:w-1/3 max-lg:mb-5">
                <FormField label="Nombre del producto" required :error="form.errors.name">
                    <FormControl v-model="form.name" placeholder="Ej. Tomate Criollo" />
                </FormField>
            </div>

            <div class="md:w-1/3 max-lg:mb-5">
                <FormField label="Costo por Unidad (Kg)" required :error="form.errors.price">
                    <FormControl v-model="form.price" type="number" height="h-10" placeholder="Ej. 15.50" />
                </FormField>
            </div>
            
            <div class="md:w-1/3 max-lg:mb-5">
                <FormField label="Categoría" required :error="form.errors.category_id">
                    <FormControl v-model="form.category_id" type="select" :options="categories"
                        placeholder="Seleccione una categoría" :error="form.errors.category_id" />
                </FormField>
            </div>
        </div>

        <div class="md:w-full mb-5">
            <FormField label="Descripción del Producto" :error="form.errors.description">
                <FormControl v-model="form.description" type="textarea"
                    placeholder="Describa el producto, su origen, y beneficios." :error="form.errors.description" />
            </FormField>
        </div>
        <CardSection title="Imágenes relacionadas" description="Agrega imágenes relacionadas con el producto"
            iconClasses="bg-forest-400 text-mono-100" :icon="mdiImageOutline">
            <FormField label="Máximo 5 imágenes por publicación" :error="form.errors.photos">
                <ImageUploadCarousel :form="form" />
            </FormField>
        </CardSection>
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
const categories = inject('categories')

</script>
