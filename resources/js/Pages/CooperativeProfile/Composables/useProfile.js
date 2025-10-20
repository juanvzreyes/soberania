import { error422 } from "@/Hooks/useErrorsForm";
import { useLoading } from "@/Hooks/useLoading";
import { useForm } from "@inertiajs/vue3";
import { provide } from "vue";

export const useProfile = (props = {}) => {
    const { isLoading, setLoading } = useLoading();
    const cooperative = props.cooperative || {};
    const location = cooperative?.location || {};

    const form = useForm({
        _method: 'patch',
        name: cooperative.name || null,
        region: cooperative.region || null,
        members: cooperative.members || null,
        location: {
            state_id: location?.state_id || null,
            municipality_id: location?.municipality_id || null,
            neighborhood_id: location?.neighborhood_id || null,
            postal_code: location?.postal_code || null,
            street: location?.street || null,
            exterior_number: location?.exterior_number || null,
            interior_number: location?.interior_number || null,
            latitude: location?.latitude || null,
            longitude: location?.longitude || null,
        },
        phones: cooperative.phones || [],
    });

    const updateForm = () => {
        form.post(route(`${props.routeName}update`), {
            onStart: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
            onError: () => error422(),
        });
    };

    provide('form', form);
    provide('props', props);

    return {
        updateForm,
    };
};
