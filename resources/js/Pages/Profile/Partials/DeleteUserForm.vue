<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2 } from 'lucide-vue-next';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Eliminar Cuenta</CardTitle>
            <CardDescription>
                Una vez tu cuenta es eliminada, todos los recursos y datos serán
                permanentemente eliminados. Antes de eliminar tu cuenta, por
                favor descarga cualquier información o datos que desees
                conservar.
            </CardDescription>
        </CardHeader>
        <CardFooter>
            <Dialog
                :open="confirmingUserDeletion"
                @update:open="confirmingUserDeletion = $event"
            >
                <DialogTrigger as-child>
                    <Button variant="destructive">Eliminar Cuenta</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle
                            >¿Estás seguro de que deseas eliminar tu
                            cuenta?</DialogTitle
                        >
                        <DialogDescription>
                            Una vez tu cuenta es eliminada, todos los recurso y
                            datos serán permanentemente eliminados. Por favor
                            ingresa tu contraseña para confirmar que deseas
                            eliminar tu cuenta.
                        </DialogDescription>
                    </DialogHeader>
                    <div class="mt-6 space-y-2">
                        <Label for="password-delete" class="sr-only"
                            >Password</Label
                        >
                        <Input
                            id="password-delete"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full dark:bg-gray-900/50 dark:border-gray-700 dark:text-white"
                            placeholder="Contraseña"
                            @keyup.enter="deleteUser"
                        />
                        <p
                            v-if="form.errors.password"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>
                    <DialogFooter class="mt-6 flex justify-end gap-2">
                        <DialogClose as-child>
                             <Button variant="outline" @click="closeModal">
                                Cancelar
                            </Button>
                        </DialogClose>
                        <Button
                            variant="destructive"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="deleteUser"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="w-4 h-4 mr-2 animate-spin"
                            />
                            Eliminar Cuenta
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </CardFooter>
    </Card>
</template>
