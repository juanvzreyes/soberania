<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeadLogo from '@/Components/HeadLogo.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Eye, EyeOff, Loader2 } from 'lucide-vue-next';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <LandingLayout>
        <HeadLogo title="Restablecer Contraseña" />
        <div class="flex items-center justify-center min-h-[calc(100vh-50px)] py-12 px-4">
            <Card
                class="w-full max-w-md mx-auto border-emerald-200 shadow-lg dark:border-emerald-800 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm">
                <CardHeader class="text-center">
                    <div class="mx-auto bg-emerald-100 rounded-full p-3 w-fit dark:bg-emerald-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-emerald-600 dark:text-emerald-400 lucide lucide-key-round">
                            <path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z" />
                            <circle cx="16.5" cy="7.5" r=".5" fill="currentColor" />
                        </svg>
                    </div>
                    <CardTitle class="text-2xl font-bold text-gray-800 dark:text-white mt-4">
                        Restablecer Contraseña
                    </CardTitle>
                    <CardDescription class="text-gray-600 dark:text-gray-400">
                        Ingresa tu nueva contraseña para recuperar el acceso a tu cuenta.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="flex flex-col space-y-1.5">
                            <Label for="email" class="text-gray-700 dark:text-gray-300">Email</Label>
                            <Input id="email" type="email" v-model="form.email" required readonly
                                class="dark:bg-gray-800/50 dark:border-gray-700 dark:text-gray-400 cursor-not-allowed" />
                            <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">{{ form.errors.email }}</p>
                        </div>

                        <div class="flex flex-col space-y-1.5">
                            <Label for="password" class="text-gray-700 dark:text-gray-300">Nueva Contraseña</Label>
                             <div class="relative">
                                <Input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password"
                                    required autocomplete="new-password" placeholder="••••••••"
                                    class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white pr-10" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <EyeOff v-if="showPassword" class="h-5 w-5" />
                                    <Eye v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-sm text-red-500 mt-1">{{ form.errors.password }}</p>
                        </div>

                        <div class="flex flex-col space-y-1.5">
                             <Label for="password_confirmation" class="text-gray-700 dark:text-gray-300">Confirmar Contraseña</Label>
                             <div class="relative">
                                <Input id="password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'"
                                    v-model="form.password_confirmation" required autocomplete="new-password" placeholder="••••••••"
                                    class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white pr-10" />
                                <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation"
                                    class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <EyeOff v-if="showPasswordConfirmation" class="h-5 w-5" />
                                    <Eye v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <p v-if="form.errors.password_confirmation" class="text-sm text-red-500 mt-1">{{ form.errors.password_confirmation }}</p>
                        </div>

                        <div class="flex flex-col pt-4">
                            <Button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white"
                                :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                                <span v-else>Actualizar Contraseña</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </LandingLayout>
</template>