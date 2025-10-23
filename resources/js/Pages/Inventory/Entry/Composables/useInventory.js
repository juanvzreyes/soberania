import { error422, messageConfirm } from "@/Hooks/useErrorsForm";
import { useLoading } from "@/Hooks/useLoading";
import { router, useForm } from "@inertiajs/vue3";
import { computed, provide } from "vue";

export const useInventory = (props) => {
const { isLoading, setLoading } = useLoading();

    const inventoryEntry = props.inventoryEntry || {}; 
    const form = useForm({
        _method: inventoryEntry.id ? "patch" : "post", 
        product_id: inventoryEntry.product_id ?? null,
        quantity: inventoryEntry.quantity ?? null, 
        reason: inventoryEntry.reason ?? null,
    });

    const storeForm = () => {
        form.post(route(`${props.routeName}store`), {
            onBefore: () => setLoading(true),
            onError: (errors) => error422(errors),
            onFinish: () => setLoading(false),
        });
    };

    const updateForm = () => {
        form.post(route(`${props.routeName}update`, inventoryEntry?.id), {
            onBefore: () => setLoading(true),
            onError: (errors) => error422(errors),
            onFinish: () => setLoading(false),
        });
    };
    
    const deleteForm = async (entryId) => { 
        if (!entryId) return;
        const result = await messageConfirm();
        if (!result.isConfirmed) return;

        router.delete(route(`${props.routeName}destroy`, entryId));
    };

    provide("form", form);
    provide("products", props.products);

    return {
        
        // attributes
        processing: computed(() => form.processing),
        // methods
        storeForm,
        updateForm,
    };
};
