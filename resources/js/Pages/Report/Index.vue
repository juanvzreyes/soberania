<template>
    <HeadLogo :title="title" />
    <AuthenticatedLayout>
        <SectionTitleLineWithButton :icon="mdiChartBar" :title="title" main>
            <div class="flex items-center gap-2">
                <Button as="a" :href="route(`${routeName}excel`)" variant="outline">
                    <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                        <path :d="mdiFileExcel" />
                    </svg>
                    Exportar a Excel
                </Button>

                <Button as="a" :href="route(`${routeName}pdf`)" variant="outline">
                    <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                        <path :d="mdiFilePdfBox" />
                    </svg>
                    Exportar a PDF
                </Button>
            </div>
        </SectionTitleLineWithButton>

        <div class="p-4 mx-auto space-y-6 sm:p-6 lg:p-8">

            <Alert v-if="producers.length === 0" variant="destructive">
                <TriangleAlert class="w-4 h-4" />
                <AlertTitle>No hay datos</AlertTitle>
                <AlertDescription>
                    No se encontraron productores registrados para mostrar en el reporte.
                </AlertDescription>
            </Alert>

            <Card v-for="producer in producers" :key="producer.id">
                <CardHeader>
                    <CardTitle>{{ producer.name }}</CardTitle>
                    <CardDescription>Información de contacto y productos.</CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 md:grid-cols-2">
                    <div class="p-4 border rounded-lg bg-muted/20">
                        <h3 class="flex items-center mb-2 font-semibold">
                            <MapPin class="w-4 h-4 mr-2" />
                            Ubicación
                        </h3>
                        <p v-if="producer.producer && producer.producer.location" class="text-sm text-muted-foreground">
                            {{ producer.producer.location.street || 'Calle no especificada' }},
                            {{ producer.producer.location.exterior_number || 'SN' }}, C.P.
                            {{ producer.producer.location.postal_code || '' }}
                        </p>
                        <p v-else class="text-sm text-muted-foreground">
                            Ubicación no registrada.
                        </p>
                    </div>
                    <div class="p-4 border rounded-lg bg-muted/20">
                        <h3 class="flex items-center mb-2 font-semibold">
                            <Package class="w-4 h-4 mr-2" />
                            Productos que ofrece
                        </h3>
                        <ul v-if="producer.products.length > 0" class="pl-5 text-sm list-disc text-muted-foreground">
                            <li v-for="product in producer.products" :key="product.id">
                                {{ product.name }} - ${{ Number(product.price).toFixed(2) }}
                            </li>
                        </ul>
                        <p v-else class="text-sm text-muted-foreground">
                            Este productor aún no tiene productos registrados.
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="chartData && chartData.url">
                <CardHeader>
                    <CardTitle>Gráfica de Productos por Productor</CardTitle>
                </CardHeader>
                <CardContent class="flex items-center justify-center">
                    <img :src="chartData.url" alt="Gráfica de Productos" class="max-w-full rounded-lg h-auto">
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { TriangleAlert, MapPin, Package } from 'lucide-vue-next';
import { mdiChartBar, mdiFileExcel, mdiFilePdfBox } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
    producers: {
        type: Array,
        default: () => []
    },
    chartData: {
        type: Object,
        default: () => ({})
    },
});

</script>