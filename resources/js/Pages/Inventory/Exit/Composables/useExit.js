import { error422, messageConfirm } from "@/Hooks/useErrorsForm";
import { useLoading } from "@/Hooks/useLoading";
import { router, useForm } from "@inertiajs/vue3";
import { computed, provide, ref } from "vue"; 

export const useExit = (props) => {
    const { isLoading, setLoading } = useLoading();
    const inventoryExit = props.inventoryExit || {};
    const productsList = ref(props.products); 

    const form = useForm({
        _method: inventoryExit.id ? "patch" : "post", 
        product_id: inventoryExit.product_id ?? null,
        quantity: null, 
        reason: inventoryExit.reason ?? null,
    });

    const currentStock = computed(() => {
        if (!form.product_id) {
            return 0;
        }
        const selectedProduct = productsList.value.find(
            p => p.id === form.product_id
        );
        return selectedProduct ? selectedProduct.stock_quantity : 0;
    });

    const storeForm = () => {
        if (form.quantity > currentStock.value) {
            window.Swal.fire({
                icon: 'error',
                title: 'Stock Insuficiente',
                text: `Solo hay ${currentStock.value} unidades disponibles para dar de baja.`,
            });
            return;
        }
        
        form.post(route(`${props.routeName}store`), {
            onBefore: () => setLoading(true),
            onError: (errors) => error422(errors),
            onFinish: () => setLoading(false),
        });
    };

    const updateForm = () => {
        form.post(route(`${props.routeName}update`, inventoryExit?.id), {
            onBefore: () => setLoading(true),
            onError: (errors) => error422(errors),
            onFinish: () => setLoading(false),
        });
    };
    
    const deleteForm = async (exitId) => { 
        if (!exitId) return;
        const result = await messageConfirm();
        if (!result.isConfirmed) return;

        router.delete(route(`${props.routeName}destroy`, exitId));
    };

    provide("form", form);
    provide("products", productsList); 
    provide("currentStock", currentStock); 

    return {
        // attributes
        processing: computed(() => form.processing),
        currentStock,
        // methods
        storeForm,
        updateForm,
        deleteForm,
    };
};