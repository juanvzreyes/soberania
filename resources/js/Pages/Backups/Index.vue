<template>
    <section>
        <HeadLogo :title="title" />
        <AuthenticatedLayout>
            <SectionTitleLineWithButton :icon="mdiDatabaseCog" :title="title" main />
            <CardBox class="mb-6">
                <h4 class="text-lg font-semibold mb-4 text-forest-400">Información de la Base de Datos</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                            <BaseIcon :path="mdiDatabase" :size="32" class="text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nombre de la BD</p>
                            <p class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ dbInfo.name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <BaseIcon :path="mdiHarddisk" :size="32" class="text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tamaño Total</p>
                            <p class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ dbInfo.size }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/40 rounded-lg">
                            <BaseIcon :path="mdiTable" :size="32" class="text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tablas</p>
                            <p class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ dbInfo.tables_count }}</p>
                        </div>
                    </div>
                </div>
            </CardBox>
            <CardBox class="mb-6">
                <h4 class="text-lg font-semibold mb-4 text-forest-400">Acciones Rápidas</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        class="p-6 border-2 border-dashed border-blue-300 dark:border-blue-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                                <BaseIcon :path="mdiDownload" :size="32" class="text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h5 class="font-semibold text-lg mb-2 text-gray-900 dark:text-gray-100">Exportar Base de
                                    Datos</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Genera un respaldo completo de la base de datos en formato .sql con fecha y hora.
                                </p>
                                <BaseButton  :icon="mdiDownload" label="Crear Respaldo"
                                    @click="exportDatabase" :disabled="isExporting" />
                            </div>
                        </div>
                    </div>
                    <div
                        class="p-6 border-2 border-dashed border-orange-300 dark:border-orange-700 rounded-lg hover:border-orange-500 dark:hover:border-orange-500 transition-colors">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/40 rounded-lg">
                                <BaseIcon :path="mdiRestore" :size="32" class="text-orange-600 dark:text-orange-400" />
                            </div>
                            <div class="flex-1">
                                <h5 class="font-semibold text-lg mb-2 text-gray-900 dark:text-gray-100">Restaurar Base
                                    de
                                    Datos</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Carga un archivo de respaldo .sql para restaurar la base de datos. Se creará un
                                    respaldo automático antes.
                                </p>
                                <input ref="fileInput" type="file" accept=".sql" @change="handleFileSelect"
                                    class="hidden" />
                                <BaseButton  :icon="mdiRestore" label="Seleccionar Archivo"
                                    @click="$refs.fileInput.click()" :disabled="isRestoring" />
                            </div>
                        </div>
                    </div>
                </div>
            </CardBox>
        </AuthenticatedLayout>
    </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiDatabaseCog,
    mdiDatabase,
    mdiHarddisk,
    mdiTable,
    mdiDownload,
    mdiRestore
} from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseIcon from "@/Components/BaseIcon.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import axios from "axios";

const props = defineProps({
    title: { type: String, required: true },
    routeName: { type: String, required: true },
    dbInfo: { type: Object, required: true },
    flash: { type: Object, default: () => ({}) },
});

const isExporting = ref(false);
const isRestoring = ref(false);
const fileInput = ref(null);

const exportDatabase = async () => {
    const result = await Swal.fire({
        title: 'Crear Respaldo',
        html: `
            <div class="text-left space-y-3">
                <p class="text-gray-700">Se creará un respaldo completo de la base de datos.</p>
                <div class="bg-blue-50 p-3 rounded border-l-4 border-blue-400">
                    <p class="text-sm text-gray-700"><strong>Base de datos:</strong> ${props.dbInfo.name}</p>
                    <p class="text-sm text-gray-700"><strong>Tamaño:</strong> ${props.dbInfo.size}</p>
                    <p class="text-sm text-gray-700"><strong>Tablas:</strong> ${props.dbInfo.tables_count}</p>
                </div>
                <p class="text-xs text-gray-500">El archivo se guardará en el servidor con fecha y hora.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, Crear Respaldo',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        Swal.fire({
            title: 'Creando respaldo...',
            html: 'Por favor espera mientras se crea el respaldo de la base de datos.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        isExporting.value = true;

        try {
            const response = await axios.post(route(`${props.routeName}export`));

            isExporting.value = false;

            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Respaldo Creado!',
                    html: `
                        <div class="text-left space-y-2">
                            <p class="text-gray-700">${response.data.message}</p>
                            <div class="bg-green-50 p-3 rounded border-l-4 border-green-400 mt-3">
                                <p class="text-sm text-gray-700"><strong>Archivo:</strong> ${response.data.data.filename}</p>
                                <p class="text-sm text-gray-700"><strong>Tamaño:</strong> ${response.data.data.file_size}</p>
                                <p class="text-sm text-gray-700"><strong>Fecha:</strong> ${response.data.data.timestamp}</p>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#10B981'
                });
            }
        } catch (error) {
            isExporting.value = false;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'Ocurrió un error al crear el respaldo.',
                confirmButtonColor: '#EF4444'
            });
        }
    }
};

const handleFileSelect = async (event) => {
    const file = event.target.files[0];
    if (!file) return;
    if (!file.name.endsWith('.sql')) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo Inválido',
            text: 'Solo se permiten archivos .sql',
            confirmButtonColor: '#EF4444'
        });
        fileInput.value.value = '';
        return;
    }
    const maxSize = 100 * 1024 * 1024;
    if (file.size > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo Muy Grande',
            text: 'El archivo no debe superar los 100 MB',
            confirmButtonColor: '#EF4444'
        });
        fileInput.value.value = '';
        return;
    }

    const result = await Swal.fire({
        title: 'Restaurar Base de Datos',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded">
                    <p class="text-sm font-bold text-red-800 mb-1">¡ADVERTENCIA!</p>
                    <p class="text-sm text-red-700">Esta acción reemplazará TODOS los datos actuales de la base de datos.</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm text-gray-700 mb-2"><strong>Archivo seleccionado:</strong></p>
                    <p class="text-sm font-mono bg-white p-2 rounded border text-gray-800 break-all">${file.name}</p>
                    <p class="text-xs text-gray-600 mt-2">Tamaño: <strong>${formatBytes(file.size)}</strong></p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                    <p class="text-sm text-yellow-800 font-semibold mb-2">Medidas de seguridad:</p>
                    <ul class="text-xs text-yellow-700 space-y-1 list-disc list-inside">
                        <li>Se creará un respaldo automático antes de restaurar</li>
                        <li>Todos los usuarios serán desconectados temporalmente</li>
                        <li>El proceso puede tardar varios minutos</li>
                        <li>No cierres ni actualices esta página</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F59E0B',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, Restaurar Ahora',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        Swal.fire({
            title: 'Restaurando base de datos...',
            html: 'Este proceso puede tardar varios minutos. Por favor no cierres esta ventana.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        isRestoring.value = true;
        const formData = new FormData();
        formData.append('backup_file', file);

        try {
            const response = await axios.post(route(`${props.routeName}restore`), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            isRestoring.value = false;
            fileInput.value.value = '';

            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Restauración Exitosa!',
                    html: `
                        <div class="text-left space-y-2">
                            <p class="text-gray-700">${response.data.message}</p>
                            <div class="bg-green-50 p-3 rounded border-l-4 border-green-400 mt-3">
                                <p class="text-sm text-gray-700"><strong>Respaldo automático:</strong> ${response.data.data.auto_backup}</p>
                                <p class="text-sm text-gray-700"><strong>Fecha:</strong> ${response.data.data.timestamp}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">La página se recargará en breve...</p>
                        </div>
                    `,
                    confirmButtonColor: '#10B981',
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.reload();
                });
            }
        } catch (error) {
            isRestoring.value = false;
            fileInput.value.value = '';

            Swal.fire({
                icon: 'error',
                title: 'Error al Restaurar',
                text: error.response?.data?.message || 'Ocurrió un error al restaurar la base de datos. Verifica el archivo e inténtalo nuevamente.',
                confirmButtonColor: '#EF4444'
            });
        }
    }
};

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

onMounted(() => {
    const flash = props.flash || {};
    if (flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: props.flash.success,
            confirmButtonColor: '#10B981'
        });
    } else if (flash.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: props.flash.error,
            confirmButtonColor: '#EF4444'
        });
    } else if (flash.warning) {
        Swal.fire({
            icon: 'warning',
            title: 'Aviso',
            text: props.flash.warning,
            confirmButtonColor: '#F59E0B'
        });
    }
});
</script>