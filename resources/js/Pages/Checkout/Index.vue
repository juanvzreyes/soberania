<template>
    <AuthenticatedLayout>
        <section class="min-h-screen bg-gray-50 dark:bg-slate-900 pt-16 pb-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    Finalizar Compra
                </h1>
                <Stepper :steps="['Información del pedido', 'Opciones de Entrega', 'Método de pago']"
                    :current-step="currentStep" />
                <form @submit.prevent="handleSubmit" class="mt-8">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6">
                        <div v-show="currentStep === 0">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                                Información del comprador
                            </h2>

                            <div class="space-y-4">
                                <FormField label="Nombre completo" required label-for="buyer_name">
                                    <FormControl v-model="form.buyer_name" type="text" id="buyer_name" required
                                        placeholder="Tu nombre completo" />
                                </FormField>

                                <FormField label="Correo electrónico" required label-for="buyer_email">
                                    <FormControl v-model="form.buyer_email" type="email" id="buyer_email" required
                                        placeholder="ejemplo@correo.com" />
                                </FormField>

                                <FormField label="Teléfono de contacto" required label-for="buyer_phone">
                                    <FormControl v-model="form.buyer_phone" type="tel" id="buyer_phone" required
                                        placeholder="55-1234-5678" />
                                </FormField>
                            </div>
                            <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-700 rounded-lg">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                    Resumen del pedido
                                </h3>
                                <div class="space-y-2">
                                    <div v-for="item in cartItems" :key="item.id"
                                        class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                                        <span>{{ item.name }} x{{ item.quantity }}</span>
                                        <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                                    </div>
                                    <div class="border-t border-gray-300 dark:border-gray-600 pt-2 mt-2">
                                        <div
                                            class="flex justify-between font-bold text-lg text-gray-800 dark:text-gray-100">
                                            <span>Total a pagar:</span>
                                            <span class="text-forest-600 dark:text-forest-400">${{ subtotal }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-show="currentStep === 1">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                                Opciones de Entrega
                            </h2>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tipo de entrega *
                                    </label>
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <input v-model="form.delivery_type" type="radio" value="domicilio"
                                                class="mr-3 text-forest-600 focus:ring-forest-500" required />
                                            <span class="text-gray-700 dark:text-gray-300">Entrega a domicilio</span>
                                        </label>
                                        <label
                                            class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <input v-model="form.delivery_type" type="radio" value="recoleccion"
                                                class="mr-3 text-forest-600 focus:ring-forest-500" required />
                                            <span class="text-gray-700 dark:text-gray-300">Recoger en punto de
                                                entrega</span>
                                        </label>
                                    </div>
                                </div>

                                <div v-if="form.delivery_type === 'domicilio'">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Dirección de entrega *
                                    </label>
                                    <textarea v-model="form.delivery_address" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                                               dark:bg-slate-700 dark:text-gray-100 focus:ring-2 focus:ring-forest-500"
                                        placeholder="Calle, número, colonia, código postal..."
                                        :required="form.delivery_type === 'domicilio'"></textarea>
                                </div>
                            </div>
                        </div>
                        <div v-show="currentStep === 2">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                                Método de pago
                            </h2>

                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label
                                        class="flex items-start p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700">
                                        <input v-model="form.payment_method" type="radio" value="contra_entrega"
                                            class="mt-1 mr-3 text-forest-600 focus:ring-forest-500" required />
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Pago contra
                                                entrega</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Paga en efectivo cuando recibas tu pedido
                                            </p>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-start p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700">
                                        <input v-model="form.payment_method" type="radio" value="transferencia"
                                            class="mt-1 mr-3 text-forest-600 focus:ring-forest-500" required />
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Transferencia
                                                bancaria</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Realiza una transferencia a nuestra cuenta
                                            </p>
                                        </div>
                                    </label>
                                </div>
                                <div v-if="form.payment_method === 'transferencia'"
                                    class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">
                                        Datos para transferencia
                                    </h3>
                                    <div class="text-sm space-y-1 text-blue-800 dark:text-blue-200">
                                        <p><strong>Banco:</strong> Banco Ejemplo</p>
                                        <p><strong>Titular:</strong> AgroConecta</p>
                                        <p><strong>Cuenta:</strong> 1234 5678 9012 3456</p>
                                        <p><strong>CLABE:</strong> 012345678901234567</p>
                                        <p class="mt-2 text-xs">
                                            * Envía tu comprobante por correo o WhatsApp después de realizar el pedido
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <BaseButtons class="mt-6" type="justify-between">
                            <BaseButton v-if="currentStep > 0" label="Anterior" :icon="mdiArrowLeft" color=""
                                @click="currentStep--" />
                            <div v-else></div>

                            <BaseButton v-if="currentStep < 2" label="Siguiente" :icon="mdiArrowRight" color=""
                                @click="nextStep" />
                            <BaseButton v-else label="Confirmar pedido" :icon="mdiCheckCircle" color="" type="submit"
                                :processing="processing" />
                        </BaseButtons>
                    </div>
                </form>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Stepper from "@/Components/Stepper.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { mdiArrowLeft, mdiArrowRight, mdiCheckCircle } from "@mdi/js";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
const props = defineProps({
    cartItems: Array,
    subtotal: String,
});

const currentStep = ref(0);
const processing = ref(false);

const form = reactive({
    buyer_name: '',
    buyer_email: '',
    buyer_phone: '',
    delivery_type: '',
    delivery_address: '',
    payment_method: '',
});

const nextStep = () => {
    if (currentStep.value === 0) {
        if (!form.buyer_name || !form.buyer_email || !form.buyer_phone) {
            alert('Por favor completa todos los campos requeridos');
            return;
        }
    }

    if (currentStep.value === 1) {
        if (!form.delivery_type) {
            alert('Por favor selecciona un tipo de entrega');
            return;
        }
        if (form.delivery_type === 'domicilio' && !form.delivery_address) {
            alert('Por favor ingresa tu dirección de entrega');
            return;
        }
    }

    currentStep.value++;
};

const handleSubmit = () => {
    processing.value = true;
    router.post(route('checkout.store'), form, {
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>