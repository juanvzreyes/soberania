<template>
    <div class="chart-container">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    BarController,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    BarController,
    Title,
    Tooltip,
    Legend
);

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
    }
});

const chartCanvas = ref(null);
let chartInstance = null;

const createChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    if (!chartCanvas.value) return;

    const ctx = chartCanvas.value.getContext('2d');
    
    chartInstance = new ChartJS(ctx, {
        type: 'bar',
        data: props.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y + ' unidades';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        padding: 10,
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        padding: 10,
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            },
            ...props.options
        }
    });
};

onMounted(() => {
    createChart();
});

watch(() => props.data, () => {
    createChart();
}, { deep: true });

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>

<style scoped>
.chart-container {
    position: relative;
    height: 100%;
    width: 100%;
    min-height: 300px;
}

canvas {
    max-height: 100%;
}
</style>