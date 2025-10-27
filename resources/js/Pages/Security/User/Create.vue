<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>

        <DataForm :form="form" :roles="roles">
            <template #actions>
                <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="" label="Cancelar" />
                <BaseButton @click="saveForm" :icon="mdiCheck" color="" label="Guardar" :processing="form.processing" />
            </template>
        </DataForm>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPlus, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "./DataForm.vue";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
    roles: {
        type: Object,
        required: true
    },
});

const form = useForm({
    name: null,
    first_name: null,
    last_name: null,
    second_last_name: null,
    email: null,
    password: null,
    roles: [],
});

const saveForm = () => {
    form.post(route(`${props.routeName}store`));
};
</script>