<template>
    <AuthenticatedLayout>
        <HeadLogo :title="title" />
        <SectionTitleLineWithButton :icon="mdiPencil" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>
        <CardBox isForm @submit.prevent="updateForm">
            <DataForm :form="form" />
            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />

                    <BaseButton @click="updateForm" :icon="mdiCheck" color="success" label="Actualizar Categoría"
                        :processing="form.processing" class="!bg-green-600 !text-white" />
                </BaseButtons>
            </template>
        </CardBox>
    </AuthenticatedLayout>

</template>

<script setup>
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPencil, mdiClose, mdiCheck, mdiTrashCan } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "../Components/Dataform.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
    title: { type: String, required: true },
    routeName: { type: String, required: true },
    category: { type: Object, required: true },
});
const categoryData = props.category.data ?? props.category;

const form = useForm({
    id: categoryData.id,
    name: categoryData.name,
    description: categoryData.description, 
    is_active: categoryData.is_active, 
});

const updateForm = () => {
    form.put(route(`${props.routeName}update`, {
        category: categoryData.id
    }));
};

</script>