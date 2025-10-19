<template>

    <AuthenticatedLayout>
        <HeadLogo :title="title" />

        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>
        <CardBox isForm @submit.prevent="saveForm">
            <DataForm :form="form" />
            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
                    <BaseButton @click="saveForm" :icon="mdiCheck" color="success" label="Guardar Categoría"
                        :processing="form.processing" class="!bg-green-600 !text-white" />
                </BaseButtons>
            </template>
        </CardBox>
    </AuthenticatedLayout>
</template>

<script setup>
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPlus, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "../Components/Dataform.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
    title: { type: String, required: true },
    routeName: { type: String, required: true },
});

const form = useForm({
    name: '',
    description: '',
    is_active: true,
});

const saveForm = () => {
    form.post(route(`${props.routeName}store`));
};
</script>