<script setup>
import LocationsMap from '@/Components/LocationsMap.vue';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Download, MapPin } from 'lucide-vue-next';
import CardFooter from '@/Components/ui/card/CardFooter.vue';
import Button from '@/components/ui/button/Button.vue';
import { mdiFileExcel, mdiFilePdfBox } from '@mdi/js';

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
                <CardFooter class="flex justify-center pt-6">
                    <div class="flex items-center gap-3">
                        <a :href="route('producer.report.pdf')">
                            <Button variant="outline" class="text-emerald-700 border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 
               dark:text-emerald-400 dark:border-emerald-600 dark:hover:bg-emerald-900/50 
               dark:hover:text-emerald-300 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 fill-current" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path :d="mdiFilePdfBox" />
                                </svg>
                                Descargar Lista de Productores (PDF)
                            </Button>
                        </a>

                        <a :href="route('producer.report.excel')">
                            <Button variant="outline" class="text-emerald-700 border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 
               dark:text-emerald-400 dark:border-emerald-600 dark:hover:bg-emerald-900/50 
               dark:hover:text-emerald-300 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 fill-current" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path :d="mdiFileExcel" />
                                </svg>
                                Descargar Lista de Productores (Excel)
                            </Button>
                        </a>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </LandingLayout>
</template>