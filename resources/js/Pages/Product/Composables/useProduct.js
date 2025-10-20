import { error422, messageConfirm } from "@/Hooks/useErrorsForm";
import { useLoading } from "@/Hooks/useLoading";
import { router, useForm } from "@inertiajs/vue3";
import { computed, provide } from "vue";

export const useProduct = (props) => {
    const { isLoading, setLoading } = useLoading();
    const product = props.product || {};

    const form = useForm({
        _method: product.id ? "patch" : "post",

        photos: product?.photos ?? [],
        name: product.name ?? "",
        description: product.description ?? "",
        price: product.price ?? null,
        category_id: product?.category?.id ?? null,
    });

    const storeForm = () => {
        form.post(route(`${props.routeName}store`), {
            onBefore: () => setLoading(true),
            onError: () => error422(),
            onFinish: () => setLoading(false),
        });
    };

    const updateForm = () => {
        form.post(route(`${props.routeName}update`, product?.id), {
            onBefore: () => setLoading(true),
            onError: () => error422(),
            onFinish: () => setLoading(false),
        });
    };

    provide("form", form);
    provide("categories", props.categories);

    return {
        
        // attributes
        processing: computed(() => form.processing),
        // methods
        storeForm,
        updateForm,
    };
};

export const deleteProduct = async (productId) => {
    if (!productId) return;
    const result = await messageConfirm();
    if (!result.isConfirmed) return;

    router.delete(route('products.destroy', productId));
};
