<template>

    <AuthenticatedLayout>
        <HeadLogo :title="title" />

        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>
        <DataForm>
            <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
            <BaseButton @click="updateForm" :icon="mdiCheck" color="success" label="Actualizar Producto" class="!bg-green-600 !text-white" />
        </DataForm>
    </AuthenticatedLayout>
</template>

<script setup>
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPlus, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import DataForm from "../Components/Dataform.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useProduct } from "@/Pages/Product/Composables/useProduct";

const props = defineProps({
    title: { type: String, required: true },
    routeName: { type: String, required: true },
    categories: { type: Array, required: true },
    product: { type: Object, required: true }, 
});
const { updateForm } = useProduct(props);

</script>