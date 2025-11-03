<template>
    <div class="chart-wrapper">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import {
    Chart,
    PieController,
    ArcElement,
    Tooltip,
    Legend,
    Title
} from 'chart.js'

// Registrar componentes de Chart.js necesarios para gráfica de pastel
Chart.register(
    PieController,
    ArcElement,
    Tooltip,
    Legend,
    Title
)

const props = defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({
            labels: [],
            datasets: []
        })
    },
    options: {
        type: Object,
        default: () => ({})
    },
    height: {
        type: Number,
        default: 400
    }
})

const chartCanvas = ref(null)
let chartInstance = null

const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'right',
            labels: {
                padding: 15,
                font: {
                    size: 12
                },
                usePointStyle: true,
                pointStyle: 'circle'
            }
        },
        tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
                size: 14,
                weight: 'bold'
            },
            bodyFont: {
                size: 13
            },
            padding: 12,
            displayColors: true,
            callbacks: {
                label: function(context) {
                    let label = context.label || ''
                    if (label) {
                        label += ': '
                    }
                    // Formato de moneda si es número
                    if (typeof context.parsed === 'number') {
                        label += new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(context.parsed)
                    } else {
                        label += context.parsed
                    }
                    
                    // Agregar porcentaje
                    const dataset = context.dataset
                    const total = dataset.data.reduce((acc, val) => acc + val, 0)
                    const percentage = ((context.parsed / total) * 100).toFixed(1)
                    label += ` (${percentage}%)`
                    
                    return label
                }
            }
        }
    }
}

const createChart = () => {
    if (!chartCanvas.value) return

    // Destruir gráfica existente si hay una
    if (chartInstance) {
        chartInstance.destroy()
    }

    // Validar que hay datos
    if (!props.data.labels || props.data.labels.length === 0) {
        return
    }

    const ctx = chartCanvas.value.getContext('2d')
    
    // Mergear opciones
    const mergedOptions = {
        ...defaultOptions,
        ...props.options,
        plugins: {
            ...defaultOptions.plugins,
            ...(props.options.plugins || {})
        }
    }

    chartInstance = new Chart(ctx, {
        type: 'pie',
        data: props.data,
        options: mergedOptions
    })
}

const updateChart = () => {
    if (chartInstance && props.data) {
        chartInstance.data = props.data
        chartInstance.update()
    } else {
        createChart()
    }
}

onMounted(() => {
    createChart()
})

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy()
    }
})

// Observar cambios en los datos
watch(() => props.data, () => {
    updateChart()
}, { deep: true })

// Observar cambios en las opciones
watch(() => props.options, () => {
    if (chartInstance) {
        chartInstance.options = {
            ...defaultOptions,
            ...props.options,
            plugins: {
                ...defaultOptions.plugins,
                ...(props.options.plugins || {})
            }
        }
        chartInstance.update()
    }
}, { deep: true })
</script>

<style scoped>
.chart-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 300px;
}

canvas {
    max-width: 100%;
    height: auto;
}
</style>