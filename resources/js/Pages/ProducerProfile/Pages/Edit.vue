<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <SectionTitleLineWithButton :icon="mdiPencil" :title="title" main />
        <ProfileForm>
            <BaseButton @click="updateForm" :icon="mdiContentSave" color="" label="Actualizar" />
        </ProfileForm>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPencil, mdiContentSave } from "@mdi/js";
import { useProfile } from "../Composables/useProfile";
import HeadLogo from "@/Components/HeadLogo.vue";
import ProfileForm from "../Components/ProfileForm.vue";
import BaseButton from "@/Components/BaseButton.vue";

const props = defineProps({
    producer: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
    errors: {
        type: Object,
        required: true,
    },
});


const { updateForm } = useProfile(props);
</script>