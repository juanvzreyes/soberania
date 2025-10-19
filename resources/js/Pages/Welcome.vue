<template>

    <Head title="AgroConecta | Soberanía Alimentaria en Morelos" />
    <div class="relative overflow-hidden text-black/80 dark:text-white/80">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repea"
            style="background-image: url('/img/bg-agroconecta.jpg'); background-attachment: fixed;">

        </div>
        <div class="absolute inset-0 bg-white/70 dark:bg-black/60 backdrop-blur-sm"></div>
        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-green-600 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex items-center lg:col-start-1">
                        <h1 class="text-3xl font-twogether font-bold text-green-600 dark:text-green-300">
                            AgroConecta
                        </h1>
                    </div>

                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end lg:col-start-3">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-green-600 ring-1 ring-green-600/50 transition hover:bg-green-600 hover:text-white focus:outline-none focus-visible:ring-green-600 dark:text-white dark:hover:bg-green-600 dark:hover:text-white dark:focus-visible:ring-white">
                        Mi Panel
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="rounded-full bg-amber-500 px-5 py-2 text-white font-semibold shadow-md hover:bg-amber-600 hover:shadow-lg transition-all duration-300 dark:bg-amber-400 dark:hover:bg-amber-300">
                            Iniciar Sesión
                            </Link>


                            <Link v-if="canRegister" :href="route('register')"
                                class="rounded-full px-5 py-2 ml-3 bg-lime-600 hover:bg-lime-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                            Regístrate Ahora
                            </Link>

                        </template>
                    </nav>
                </header>
                <main class="mt-12 md:mt-24">
                    <div class="text-center mb-16 lg:mb-24">
                        <h1
                            class="text-5xl md:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight drop-shadow-md">
                            AgroConecta: Cosecha Local, <span class="text-amber-500">Soberanía Alimentaria</span>.
                        </h1>
                        <p class="mt-4 text-xl text-amber-600 dark:text-amber-200 max-w-3xl mx-auto">
                            La plataforma que une a productores de Morelos, cooperativas y consumidores para un comercio
                            justo y directo. <strong>Conéctate hoy mismo</strong> para empezar a comprar o vender.
                        </p>
                        <div class="mt-8 flex justify-center space-x-4">
                            <Link v-if="canRegister" :href="route('register')"
                                class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-bold rounded-full shadow-lg text-white bg-lime-600 hover:bg-lime-500 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-lime-600/50">
                            Únete a la Red AgroConecta
                            </Link>
                        </div>
                    </div>
                    <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
                        <div
                            class="flex flex-col items-start gap-4 rounded-lg bg-white p-6 shadow-xl ring-1 ring-green-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-green-600/10 sm:size-16">
                                <svg class="text-green-600 w-8 h-8" viewBox="0 0 24 24">
                                    <path :d="mdiCarrot" fill="currentColor" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-green-600 dark:text-white">
                                Productos de Origen
                            </h2>
                            <p class="mt-1 text-sm/relaxed text-gray-600 dark:text-gray-300">
                                Accede a cosechas frescas y de alta calidad. <strong>Conectamos tu cocina directamente
                                    con el
                                    campo</strong> de Morelos, asegurando transparencia y sabor.
                            </p>
                        </div>

                        <div
                            class="flex flex-col items-start gap-4 rounded-lg bg-white p-6 shadow-xl ring-1 ring-amber-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-amber-600/10 sm:size-16">
                                <svg class="text-amber-600 w-8 h-8" viewBox="0 0 24 24">
                                    <path :d="mdiCashMultiple" fill="currentColor" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-amber-500 dark:text-white">
                                Comercio Justo y Directo
                            </h2>
                            <p class="mt-1 text-sm/relaxed text-gray-600 dark:text-gray-300">
                                Vende tus productos sin intermediarios excesivos. <strong>Mejora tus márgenes</strong> y
                                administra
                                fácilmente tu inventario y pedidos desde tu panel.
                            </p>
                        </div>

                        <div
                            class="flex flex-col items-start gap-4 rounded-lg bg-white p-6 shadow-xl ring-1 ring-purple-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-600/10 sm:size-16">
                                <svg class="text-blue-600 w-8 h-8" viewBox="0 0 24 24">
                                    <path :d="mdiChartBar" fill="currentColor" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-purple-600 dark:text-white">
                                Gestión Centralizada
                            </h2>
                            <p class="mt-1 text-sm/relaxed text-gray-600 dark:text-gray-300">
                                Herramientas robustas para la <strong>gestión de pedidos, inventario y
                                    reportes</strong>. Ideal para
                                cooperativas que requieren eficiencia logística y control.
                            </p>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black/70 dark:text-white/70">
                    AgroConecta | Promoviendo la Soberanía Alimentaria en Morelos.
                </footer>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { mdiChartBar, mdiCashMultiple, mdiCarrot } from '@mdi/js'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
})
</script>

<style scoped>
@keyframes parallaxMove {
    0% {
        background-position: center 0;
    }

    50% {
        background-position: center 40px;
    }

    100% {
        background-position: center 0;
    }
}

.animate-parallax {
    animation: parallaxMove 20s ease-in-out infinite;
    background-attachment: fixed;
}
</style>
