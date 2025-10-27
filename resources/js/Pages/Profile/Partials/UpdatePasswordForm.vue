<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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
import { Loader2 } from 'lucide-vue-next';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Actualizar Contraseña</CardTitle>
            <CardDescription>
                Asegúrate que tu cuenta use una contraseña adecuada y segura.
            </CardDescription>
        </CardHeader>

        <form @submit.prevent="updatePassword">
            <CardContent class="space-y-6">
                <div class="space-y-2">
                    <Label for="current_password">Contraseña Actual</Label>
                    <Input
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                        autocomplete="current-password"
                    />
                    <p
                        v-if="form.errors.current_password"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.current_password }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="password">Nueva Contraseña</Label>
                    <Input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                        autocomplete="new-password"
                    />
                    <p
                        v-if="form.errors.password"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="password_confirmation"
                        >Confirmar Contraseña</Label
                    >
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                        autocomplete="new-password"
                    />
                    <p
                        v-if="form.errors.password_confirmation"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.password_confirmation }}
                    </p>
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
