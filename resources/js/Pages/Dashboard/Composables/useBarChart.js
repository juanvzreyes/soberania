import { computed } from 'vue';

const chartColors = [
    { start: 'rgba(54, 162, 235, 0.8)', end: 'rgba(54, 162, 235, 0.1)' },
    { start: 'rgba(255, 99, 132, 0.8)', end: 'rgba(255, 99, 132, 0.1)' },
    { start: 'rgba(75, 192, 192, 0.8)', end: 'rgba(75, 192, 192, 0.1)' },
    { start: 'rgba(255, 206, 86, 0.8)', end: 'rgba(255, 206, 86, 0.1)' },
    { start: 'rgba(153, 102, 255, 0.8)', end: 'rgba(153, 102, 255, 0.1)' },
    { start: 'rgba(255, 159, 64, 0.8)', end: 'rgba(255, 159, 64, 0.1)' },
];

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { color: '#9CA3AF', font: { weight: 'bold' } },
            grid: { display: false },
            border: { display: false }
        },
        x: {
            ticks: { color: '#9CA3AF', font: { weight: 'bold' } },
            grid: { display: false },
            border: { display: false }
        }
    }
};

export function useBarChart(props) {
    const chartData = computed(() => {
        const stats = props.stats;

        if (!stats || !stats.topProducts || !stats.topProducts.length) {
            return { labels: [], datasets: [] };
        }

        return {
            labels: stats.topProducts.map(p => p.name),
            datasets: [{
                label: 'Unidades Vendidas',
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) {
                        return null;
                    }
                    const index = context.dataIndex;
                    const color = chartColors[index % chartColors.length];
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, color.end);
                    gradient.addColorStop(1, color.start);
                    return gradient;
                },
                borderColor: chartColors.map(c => c.start.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: 4,
                data: stats.topProducts.map(p => p.total_sold),
            }]
        };
    });

    return {
        chartData,
        chartOptions,
    };
}
