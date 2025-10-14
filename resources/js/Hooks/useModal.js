import { ref } from "vue";
import { useLoading } from "./useLoading";

export const useModal = () => {
    const isOpen = ref(false);
    const error = ref(null);
    const { isLoading, setLoading } = useLoading();

    const open = () => {
        isOpen.value = true;
        error.value = null;
    };

    const close = () => {
        isOpen.value = false;
        error.value = null;
        isLoading.value = false;
    };

    const toggle = () => {
        isOpen.value = !isOpen.value;
    };

    const setError = (errorMessage) => {
        error.value = errorMessage;
    };

    const clearError = () => {
        error.value = null;
    };

    return {
        // attributes
        isOpen,
        isLoading,
        error,
        // methods
        open,
        close,
        toggle,
        setLoading,
        setError,
        clearError,
    };
}
