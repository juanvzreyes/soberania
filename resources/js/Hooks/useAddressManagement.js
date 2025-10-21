import { ref, onMounted } from 'vue';

export function useAddressManagement(form) {
    const states = ref([]);
    const municipalities = ref([]);
    const neighborhoods = ref([]);

    const normalizeString = (str) => {
        if (!str) return '';
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    };

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
        form.value.neighborhood_id = null;
        form.value.postal_code = '';
        municipalities.value = [];
        neighborhoods.value = [];
        fetchMunicipalities(stateId);
    };

    const onMunicipalityChange = (municipalityId) => {
        form.value.neighborhood_id = null;
        form.value.postal_code = '';
        neighborhoods.value = [];
        fetchNeighborhoods(municipalityId);
    };

    const onPostalCodeChange = async (postalCode) => {
        if (!postalCode || postalCode.length !== 5) return;
        try {
            const response = await fetch(`/api/locations/postal-code/${postalCode}`);
            if (response.ok) {
                const data = await response.json();
                if (data) {
                    form.value.state_id = data.state_id;
                    await fetchMunicipalities(data.state_id);
                    form.value.municipality_id = data.municipality_id;
                    await fetchNeighborhoods(data.municipality_id);
                    form.value.neighborhood_id = data.neighborhood_id;
                }
            }
        } catch (error) {
            console.error('Error fetching postal code data:', error);
        }
    };

    const handleReverseGeocode = async (address) => {
        const postalCode = address.postcode;
        const coloniaName = address.suburb || address.neighbourhood;

        form.value.street = address.road || '';
        form.value.exterior_number = address.house_number || '';

        if (postalCode && postalCode.length === 5) {
            form.value.postal_code = postalCode;
            await onPostalCodeChange(postalCode);

            if (coloniaName && neighborhoods.value.length > 0) {
                const normalizedColoniaName = normalizeString(coloniaName);
                const matchedColonia = neighborhoods.value.find(n => 
                    normalizeString(n.name) === normalizedColoniaName
                );

                if (matchedColonia) {
                    form.value.neighborhood_id = matchedColonia.id;
                    form.value.postal_code = matchedColonia.postal_code;
                }
            }
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