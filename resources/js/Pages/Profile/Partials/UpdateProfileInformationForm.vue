<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Loader2 } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    first_name: user.first_name,
    last_name: user.last_name,
    second_last_name: user.second_last_name,
    gender: user.gender,
    email: user.email,
});
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Información de Perfil</CardTitle>
            <CardDescription>
                Actualiza tu información de perfil y dirección de correo
                electrónico.
            </CardDescription>
        </CardHeader>

        <form @submit.prevent="form.patch(route('profile.update'))">
            <CardContent class="space-y-6">
                <div class="space-y-2">
                    <Label for="first_name">Nombres</Label>
                    <Input
                        id="first_name"
                        type="text"
                        v-model="form.first_name"
                        required
                        autofocus
                        autocomplete="first_name"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                    />
                    <p
                        v-if="form.errors.first_name"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="last_name">Primer apellido</Label>
                    <Input
                        id="last_name"
                        type="text"
                        v-model="form.last_name"
                        required
                        autocomplete="last_name"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                    />
                    <p
                        v-if="form.errors.last_name"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.last_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="second_last_name">Segundo apellido</Label>
                    <Input
                        id="second_last_name"
                        type="text"
                        v-model="form.second_last_name"
                        required
                        autocomplete="second_last_name"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                    />
                    <p
                        v-if="form.errors.second_last_name"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.second_last_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="gender">Género</Label>
                    <Select v-model="form.gender">
                        <SelectTrigger
                            class="w-full dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                        >
                            <SelectValue placeholder="Selecciona tu género" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Masculino">Masculino</SelectItem>
                            <SelectItem value="Femenino">Femenino</SelectItem>
                            <SelectItem value="Otro">Otro</SelectItem>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="form.errors.gender"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.gender }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                    />
                    <p
                        v-if="form.errors.email"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <div
                    v-if="mustVerifyEmail && user.email_verified_at === null"
                >
                    <p
                        class="mt-2 text-sm text-gray-800 dark:text-gray-200"
                    >
                        Tu email no ha sido verificado.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100"
                        >
                            Click aquí para reenviar el enlace de verificación
                            de correo electrónico.
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                    >
                        Un nuevo link de verificación ha sido enviado a tu
                        dirección de correo electrónico.
                    </div>
                </div>
            </CardContent>

            <CardFooter class="flex items-center gap-4">
                <Button :disabled="form.processing">
                    <Loader2
                        v-if="form.processing"
                        class="w-4 h-4 mr-2 animate-spin"
                    />
                    Guardar
                </Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Guardado.
                    </p>
                </Transition>
            </CardFooter>
        </form>
    </Card>
</template>
