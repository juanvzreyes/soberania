<template>
    <div ref="mapContainer" class="w-full h-[400px] md:h-[550px] rounded-lg border" />
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png'
});

const props = defineProps({
    locations: {
        type: Array,
        default: () => []
    }
});

const mapContainer = ref(null);
const map = ref(null);
const markersGroup = ref(null);

const initMap = () => {
    if (mapContainer.value && !map.value) {
        const mexicoCenter = [23.6345, -102.5528];
        map.value = L.map(mapContainer.value).setView(mexicoCenter, 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map.value);

        markersGroup.value = L.featureGroup().addTo(map.value);
        addMarkers(props.locations);
    }
};

const addMarkers = (locationsList) => {
    if (!map.value || !markersGroup.value) return;

    markersGroup.value.clearLayers();
    locationsList.forEach(location => {
        if (location.latitude && location.longitude) {
            const marker = L.marker([location.latitude, location.longitude]);

            let popupContent = `<b>${location.name}</b>`;

            if (location.certification) {
                popupContent += `<br><small>Certificación: ${location.certification}</small>`;
            }

            marker.bindPopup(popupContent);
            markersGroup.value.addLayer(marker);
        }
    });

    try {
        if (locationsList.length > 0) {
            map.value.fitBounds(markersGroup.value.getBounds().pad(0.1));
        } else {
            map.value.setView([23.6345, -102.5528], 5);
        }
    } catch (e) {
        console.warn("No se pudieron ajustar los límites del mapa:", e);
        map.value.setView([23.6345, -102.5528], 5);
    }
};

watch(() => props.locations, (newLocations) => {
    if (map.value) {
        addMarkers(newLocations);
    }
}, { deep: true });

onMounted(() => {
    nextTick(() => {
        initMap();
    });
});
</script>