<script setup>
import LocationsMap from '@/Components/LocationsMap.vue';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { MapPin } from 'lucide-vue-next';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    locations: Array,
});
</script>

<template>
    <LandingLayout>
        <HeadLogo title="Mapa de Productores" />
        <div class="flex items-center justify-center min-h-[calc(120vh-50px)] py-20 px-4">
            <Card
                class="w-full max-w-6xl mx-auto border-emerald-200 shadow-lg dark:border-emerald-800 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm">
                <CardHeader class="text-center">
                    <div class="mx-auto bg-emerald-100 rounded-full p-3 w-fit dark:bg-emerald-900/50">
                        <MapPin class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <CardTitle class="text-2xl font-bold text-gray-800 dark:text-white mt-4">
                        Red de Productores
                    </CardTitle>
                    <CardDescription class="text-gray-600 dark:text-gray-400">
                        Explora la ubicación de los productores en nuestra red.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <LocationsMap :locations="props.locations" />
                </CardContent>
            </Card>
        </div>
    </LandingLayout>
</template>