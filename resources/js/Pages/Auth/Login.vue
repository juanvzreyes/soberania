<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Eye, EyeOff } from 'lucide-vue-next';

const LoaderCircle = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>`
};

const showPassword = ref(false);

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <LandingLayout>
        <HeadLogo title="Iniciar Sesión" />

        <div class="flex items-center justify-center min-h-[calc(100vh-50px)] py-12 px-4">
            <Card
                class="w-full max-w-md mx-auto border-emerald-200 shadow-lg dark:border-emerald-800 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm">
                <CardHeader class="text-center">
                    <div class="mx-auto bg-emerald-100 rounded-full p-3 w-fit dark:bg-emerald-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-emerald-600 dark:text-emerald-400">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" x2="3" y1="12" y2="12" />
                        </svg>
                    </div>
                    <CardTitle class="text-2xl font-bold text-gray-800 dark:text-white mt-4">Iniciar Sesión
                    </CardTitle>
                    <CardDescription class="text-gray-600 dark:text-gray-400">
                        Conéctate para gestionar tus cosechas y pedidos.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="status"
                        class="mb-4 text-sm font-medium p-3 rounded-md bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/50 dark:border-green-700 dark:text-green-300">
                        {{ status }}
                    </div>
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Email Field -->
                        <div class="flex flex-col space-y-1.5">
                            <Label for="email" class="text-gray-700 dark:text-gray-300">Email</Label>
                            <Input id="email" type="email" v-model="form.email" required autofocus
                                autocomplete="username" placeholder="tu-correo@ejemplo.com"
                                class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white" />
                            <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">{{ form.errors.email }}</p>
                        </div>
                        <!-- Password Field -->
                        <div class="flex flex-col space-y-1.5">
                            <Label for="password" class="text-gray-700 dark:text-gray-300">Contraseña</Label>
                            <div class="relative">
                                <Input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password"
                                    required autocomplete="current-password"
                                    class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white pr-10" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <EyeOff v-if="showPassword" class="h-5 w-5" />
                                    <Eye v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-sm text-red-500 mt-1">{{ form.errors.password }}
                            </p>
                        </div>
                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <Checkbox id="remember" :checked="form.remember"
                                    @update:checked="val => form.remember = val" />
                                <label for="remember"
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400 leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Recordarme
                                </label>
                            </div>
                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-sm text-emerald-600 hover:underline dark:text-emerald-400">
                            ¿Olvidaste tu contraseña?
                            </Link>
                        </div>
                        <div class="flex flex-col pt-4">
                            <Button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white"
                                :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" />
                                <span v-else>Iniciar Sesión</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
                <CardFooter class="flex justify-center">
                    <Link :href="route('register')" class="text-sm text-amber-600 hover:underline dark:text-amber-500">
                    ¿No tienes cuenta? <span class="font-semibold">Únete a la red</span>
                    </Link>
                </CardFooter>
            </Card>
        </div>
    </LandingLayout>
</template>
