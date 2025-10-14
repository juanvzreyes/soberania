import { ref, onMounted, watch } from 'vue';

export function useAddressManagement(form) {
    const states = ref([]);
    const municipalities = ref([]);
    const neighborhoods = ref([]);

    const fetchStates = async () => {
        try {
            const response = await fetch('/api/locations/states');
            if (response.ok) states.value = await response.json();
        } catch (error) {
            console.error('Error fetching states:', error);
        }
    };

    const fetchMunicipalities = async (stateId) => {
        if (!stateId) {
            municipalities.value = [];
            neighborhoods.value = [];
            return;
        }
        try {
            const response = await fetch(`/api/locations/states/${stateId}/municipalities`);
            if (response.ok) municipalities.value = await response.json();
        } catch (error) {
            console.error('Error fetching municipalities:', error);
        }
    };

    const fetchNeighborhoods = async (municipalityId) => {
        if (!municipalityId) {
            neighborhoods.value = [];
            return;
        }
        try {
            const response = await fetch(`/api/locations/municipalities/${municipalityId}/neighborhoods`);
            if (response.ok) neighborhoods.value = await response.json();
        } catch (error) {
            console.error('Error fetching neighborhoods:', error);
        }
    };

    const onStateChange = (stateId) => {
        form.value.municipality_id = null;
        form.value.colony_id = null;
        fetchMunicipalities(stateId);
    };

    const onMunicipalityChange = (municipalityId) => {
        form.value.colony_id = null;
        fetchNeighborhoods(municipalityId);
    };

    const onPostalCodeChange = async (postalCode) => {
        if (!postalCode || postalCode.length !== 5) return;
        try {
            const response = await fetch(`/api/locations/postal-code/${postalCode}`);
            if (response.ok) {
                const data = await response.json();
                form.value.state_id = data.state_id;
                await fetchMunicipalities(data.state_id);
                form.value.municipality_id = data.municipality_id;
                await fetchNeighborhoods(data.municipality_id);
            }
        } catch (error) {
            console.error('Error fetching postal code data:', error);
        }
    };
    
    const handleReverseGeocode = (address) => {
        const postalCode = address.postcode;
        form.value.street = address.road || '';
        form.value.exterior_number = address.house_number || '';
        if (postalCode && postalCode.length === 5) {
            onPostalCodeChange(postalCode);
        }
    };

    onMounted(fetchStates);
    
    onMounted(() => {
        if (form.value.state_id) {
            fetchMunicipalities(form.value.state_id).then(() => {
                if (form.value.municipality_id) {
                    fetchNeighborhoods(form.value.municipality_id);
                }
            });
        }
    });

    return {
        states,
        municipalities,
        neighborhoods,
        onStateChange,
        onMunicipalityChange,
        onPostalCodeChange,
        handleReverseGeocode,
    };
}