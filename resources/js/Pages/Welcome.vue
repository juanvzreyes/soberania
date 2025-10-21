<template>

    <HeadLogo title="Inicio - AgroConecta" />

    <LandingLayout>
        <main class="pt-24 md:pt-32">
            <div class="text-center mb-16 lg:mb-24">
                <h1 class="text-5xl md:text-6xl font-bold tracking-tight text-gray-800 dark:text-white leading-tight">
                    AgroConecta: Cosecha Local, <span class="text-amber-500">Soberanía Alimentaria</span>.
                </h1>
                <p class="mt-4 text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    La plataforma que une a productores de Morelos, cooperativas y consumidores para un comercio
                    justo y directo. <strong>Conéctate hoy mismo</strong> para empezar a comprar o vender.
                </p>

                <div class="mt-8 flex justify-center">
                    <Button v-if="canRegister" as-child size="lg"
                        class="rounded-full px-8 py-7 text-lg font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all">
                        <Link :href="route('register')">
                        Únete a la Red AgroConecta
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
                <Card class="bg-white/80 dark:bg-gray-800/80 border-0 shadow-lg backdrop-blur-sm">
                    <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50 sm:size-16">
                            <Carrot class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <CardTitle class="text-xl font-semibold text-emerald-600 dark:text-emerald-400">
                            Productos de Origen
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="mt-1 text-sm/relaxed text-gray-600 dark:text-gray-300">
                            Accede a cosechas frescas y de alta calidad. <strong>Conectamos tu cocina directamente
                                con el
                                campo</strong> de Morelos.
                        </p>
                    </CardContent>
                </Card>

                <Card class="bg-white/80 dark:bg-gray-800/80 border-0 shadow-lg backdrop-blur-sm">
                    <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/50 sm:size-16">
                            <Handshake class="h-8 w-8 text-amber-600 dark:text-amber-400" />
                        </div>
                        <CardTitle class="text-xl font-semibold text-amber-500 dark:text-amber-400">
                            Comercio Justo y Directo
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="mt-1 text-sm/relaxed text-gray-600 dark:text-gray-300">
                            Vende tus productos sin intermediarios excesivos. <strong>Mejora tus márgenes</strong> y
                            administra
                            fácilmente tu inventario.
                        </p>
                    </CardContent>
                </Card>

                <Card class="bg-white/80 dark:bg-gray-800/80 border-0 shadow-lg backdrop-blur-sm">
                    <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50 sm:size-16">
                            <KanbanSquare class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <CardTitle class="text-xl font-semibold text-emerald-600 dark:text-emerald-400">
                            Gestión Centralizada
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="mt-1 text-sm/relaxed text-gray-600 dark:text-gray-300">
                            Herramientas robustas para <strong>gestión de pedidos, inventario y
                                reportes</strong>. Ideal para
                            cooperativas.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </main>
    </LandingLayout>
</template>
<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import LandingLayout from '@/Layouts/LandingLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Carrot, Handshake, KanbanSquare } from 'lucide-vue-next'
import HeadLogo from '@/Components/HeadLogo.vue'

const canRegister = usePage().props.canRegister
</script>
