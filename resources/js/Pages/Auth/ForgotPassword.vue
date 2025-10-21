<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const LoaderCircle = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>`
};

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <LandingLayout>
        <HeadLogo title="Recuperar Contraseña" />
        <div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-12 px-4">
            <Card
                class="w-full max-w-md mx-auto border-emerald-200 shadow-lg dark:border-emerald-800 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm">
                <CardHeader class="text-center">
                    <div class="mx-auto bg-emerald-100 rounded-full p-3 w-fit dark:bg-emerald-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-emerald-600 dark:text-emerald-400">
                            <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3-3-3s-3 1-3 3a7 7 0 0 0 7 7" />
                            <path d="M12 14a7 7 0 0 0-7 7" />
                            <path d="M14 12a2 2 0 0 0-2-2 2 2 0 0 0-2 2" />
                            <path d="m14 6-2-2-2 2" />
                        </svg>
                    </div>
                    <CardTitle class="text-2xl font-bold text-gray-800 dark:text-white mt-4">Recupera tu Acceso
                    </CardTitle>
                    <CardDescription class="text-gray-600 dark:text-gray-400">
                        Ingresa tu email para reestablecer tu conexión con AgroConecta.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="status"
                        class="mb-4 text-sm font-medium p-3 rounded-md bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/50 dark:border-green-700 dark:text-green-300">
                        {{ status }}
                    </div>
                    <form @submit.prevent="submit">
                        <div class="grid w-full items-center gap-4">
                            <div class="flex flex-col space-y-1.5">
                                <Label for="email" class="text-gray-700 dark:text-gray-300">Email</Label>
                                <Input id="email" type="email" v-model="form.email" required autofocus
                                    autocomplete="username" placeholder="tu-correo@ejemplo.com"
                                    class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white" />
                                <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">{{ form.errors.email }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-4 mt-6">
                            <Button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white"
                                :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" />
                                <span v-else>Enviar Enlace</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
                <CardFooter class="flex justify-center">
                    <Link :href="route('login')" class="text-sm text-amber-600 hover:underline dark:text-amber-500">
                    Volver a Iniciar Sesión
                    </Link>
                </CardFooter>
            </Card>
        </div>
    </LandingLayout>
</template>
