import { router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
const debounce = (fn, delay = 400) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };
};

export const useCatalog = (initialFilters, routeName) => {
    
    const filters = reactive({
        search: initialFilters.search || '',
        category_id: initialFilters.category_id || null,
        min_price: initialFilters.min_price || null,
        max_price: initialFilters.max_price || null,
        producer_id: initialFilters.producer_id || null,
        order: initialFilters.order || 'name', 
        direction: initialFilters.direction || 'asc', 
    });

    const applyFilters = debounce(() => {
        router.get(route(`${routeName}index`), filters,  {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    }, 400);

    const clearFilters = () => {
        filters.search = '';
        filters.category_id = null;
        filters.min_price = null;
        filters.max_price = null;
        filters.producer_id = null;
        
        applyFilters();
    };
    
    
    watch(filters, () => {
        applyFilters();
    }, { deep: true });

    return {
        filters,
        applyFilters,
        clearFilters,
    };
};