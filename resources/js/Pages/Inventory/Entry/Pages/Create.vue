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
            <BaseButton @click="storeForm" :icon="mdiCheck" color="success" label="Guardar Entrada de producto"
                :processing="processing" class="!bg-green-600 !text-white" />
        </DataForm>
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
import { useInventory } from "@/Pages/Inventory/Entry/Composables/useInventory";

const props = defineProps({
    title: { type: String, required: true },
    routeName: { type: String, required: true },
    products: { type: Array, required: true },
});
const { processing, storeForm } = useInventory(props);

</script>