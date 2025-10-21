<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <FormField label="Código Postal" :error="props.errors?.['location.postal_code']">
            <FormControl v-model="form.postal_code" type="text" placeholder="12345" maxlength="5"
                @change="() => onPostalCodeChange(form.postal_code)" />
        </FormField>

        <FormField label="Estado" :error="props.errors?.['location.state_id']">
            <FormControl v-model="form.state_id" :options="states" value-select="id" value-option="name"
                placeholder="Selecciona Estado" @change="() => onStateChange(form.state_id)" />
        </FormField>

        <FormField label="Municipio" :error="props.errors?.['location.municipality_id']">
            <FormControl v-model="form.municipality_id" :options="municipalities" value-select="id" value-option="name"
                placeholder="Selecciona Municipio" :disabled="!form.state_id || municipalities.length === 0"
                @change="() => onMunicipalityChange(form.municipality_id)" />
        </FormField>

        <FormField label="Colonia" :error="props.errors?.['location.neighborhood_id']">
            <FormControl v-model="form.neighborhood_id" :options="filteredNeighborhoods" value-select="id"
                value-option="name" placeholder="Selecciona Colonia"
                :disabled="!form.municipality_id || neighborhoods.length === 0" />
        </FormField>

        <FormField label="Calle" :error="props.errors?.['location.street']">
            <FormControl v-model="form.street" type="text" placeholder="Calle principal" />
        </FormField>

        <FormField label="Número Exterior" :error="props.errors?.['location.exterior_number']">
            <FormControl v-model="form.exterior_number" type="text" placeholder="Número exterior" />
        </FormField>

        <FormField label="Número Interior" :error="props.errors?.['location.interior_number']">
            <FormControl v-model="form.interior_number" type="text" placeholder="Número interior (opcional)" />
        </FormField>

        <div class="md:col-span-2">
            <FormField label="Ubicación en el mapa">
                <div class="space-y-4">
                    <div class="flex gap-2">
                        <BaseButton @click="geocodeAddress" :icon="mdiMagnify" color="bg-forest-400 text-mono-100"
                            label="Buscar dirección en mapa" small />
                    </div>
                    <div ref="mapContainer" class="w-full h-64 rounded-lg border border-gray-300"
                        style="min-height: 256px;"></div>
                </div>
            </FormField>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick, computed } from 'vue';
import { mdiMagnify } from '@mdi/js';
import FormField from './FormField.vue';
import FormControl from './FormControl.vue';
import BaseButton from './BaseButton.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useAddressManagement } from '@/Hooks/useAddressManagement.js';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png'
});

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            state_id: null,
            municipality_id: null,
            neighborhood_id: null,
            postal_code: null,
            street: null,
            interior_number: null,
            exterior_number: null,
            latitude: null,
            longitude: null
        })
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:modelValue']);

const form = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const {
    states,
    municipalities,
    neighborhoods,
    onStateChange,
    onMunicipalityChange,
    onPostalCodeChange,
    handleReverseGeocode
} = useAddressManagement(form);

const mapContainer = ref(null);
const map = ref(null);
const marker = ref(null);

const initMap = () => {
    if (mapContainer.value && !map.value) {
        const mexicoCenter = [23.6345, -102.5528];
        map.value = L.map(mapContainer.value).setView(mexicoCenter, 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map.value);
        map.value.on('click', onMapClick);
        if (form.value.latitude && form.value.longitude) {
            const coordinates = [form.value.latitude, form.value.longitude];
            map.value.setView(coordinates, 14);
            marker.value = L.marker(coordinates).addTo(map.value).bindPopup(`Ubicación guardada`);
        }
    }
};

watch(() => [form.value.latitude, form.value.longitude], ([newLat, newLon]) => {
    if (newLat && newLon && map.value) {
        const coordinates = [newLat, newLon];
        map.value.setView(coordinates, 16);
        if (marker.value) {
            marker.value.setLatLng(coordinates);
        } else {
            marker.value = L.marker(coordinates).addTo(map.value);
        }
    }
});

const filteredNeighborhoods = computed(() => {
    if (!form.value.postal_code || form.value.postal_code.length !== 5) {
        return neighborhoods.value;
    }
    return neighborhoods.value.filter(neighborhood =>
        neighborhood.postal_code === form.value.postal_code
    );
});

const onMapClick = async (e) => {
    const { lat, lng } = e.latlng;
    if (marker.value) map.value.removeLayer(marker.value);
    marker.value = L.marker([lat, lng]).addTo(map.value).bindPopup(`Ubicación seleccionada`).openPopup();
    form.value.latitude = lat;
    form.value.longitude = lng;
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
        const data = await response.json();
        if (data && data.address) handleReverseGeocode(data.address);
    } catch (error) {
        console.error("Error en la geocodificación inversa:", error);
    }
};

const geocodeAddress = async () => {
    const findNameById = (collection, id) => (collection.find(item => item.id === id) || {}).name || '';

    const stateName = findNameById(states.value, form.value.state_id);
    const municipalityName = findNameById(municipalities.value, form.value.municipality_id);
    const neighborhoodName = findNameById(neighborhoods.value, form.value.neighborhood_id);

    const params = new URLSearchParams({
        format: 'json',
        countrycodes: 'mx',
        limit: 1
    });

    if (form.value.street) {
        params.append('street', `${form.value.street} ${form.value.exterior_number || ''}`.trim());
    }
    if (neighborhoodName) {
        params.append('city', neighborhoodName);
    }
    if (municipalityName) {
        params.append('county', municipalityName);
    }
    if (stateName) {
        params.append('state', stateName);
    }
    if (form.value.postal_code) {
        params.append('postalcode', form.value.postal_code);
    }

    if (!form.value.street && !neighborhoodName && !form.value.postal_code) {
        // alert('Por favor, ingrese al menos la calle, colonia o código postal.');
        return;
    }

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?${params.toString()}`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor de geocodificación');

        const data = await response.json();

        if (data && data.length > 0) {
            const result = data[0];
            const coordinates = [parseFloat(result.lat), parseFloat(result.lon)];
            map.value.setView(coordinates, 17);

            if (marker.value) {
                map.value.removeLayer(marker.value);
            }
            marker.value = L.marker(coordinates).addTo(map.value).bindPopup(`Ubicación encontrada`).openPopup();
            form.value.latitude = coordinates[0];
            form.value.longitude = coordinates[1];
        } else {
            // alert('No se pudo encontrar la dirección en el mapa.');
        }
    } catch (error) {
        console.error("Error en la geocodificación:", error);
        // alert('Ocurrió un error al buscar la dirección.');
    }
};

onMounted(() => {
    nextTick(() => {
        initMap();
    });
});
</script>