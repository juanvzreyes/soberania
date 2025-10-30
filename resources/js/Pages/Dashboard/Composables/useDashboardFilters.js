import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useDashboardFilters(props) {
    const startDate = ref(props.filters.startDate);
    const endDate = ref(props.filters.endDate);

    watch(() => props.filters, (newFilters) => {
        startDate.value = newFilters.startDate;
        endDate.value = newFilters.endDate;
    }, { deep: true });

    const applyFilters = () => {
        router.get(route('dashboard'), {
            start_date: startDate.value,
            end_date: endDate.value,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const resetFilters = () => {
        startDate.value = props.filters.startDate;
        endDate.value = props.filters.endDate;
        applyFilters();
    };

    return {
        startDate,
        endDate,
        applyFilters,
        resetFilters,
    };
}
