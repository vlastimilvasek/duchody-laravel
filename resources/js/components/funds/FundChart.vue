<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend, Filler)

const props = defineProps({
    labels: { type: Array, required: true },
    fundReturns: { type: Array, required: true },
    inflation: { type: Array, required: true },
    fundName: { type: String, required: true },
})

const prefersReducedMotion =
    typeof window !== 'undefined'
        ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
        : false

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            label: props.fundName,
            data: props.fundReturns,
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.08)',
            pointBackgroundColor: '#059669',
            borderWidth: 2.5,
            tension: 0.35,
            fill: true,
        },
        {
            label: 'Inflace ČR',
            data: props.inflation,
            borderColor: '#94a3b8',
            backgroundColor: 'transparent',
            pointBackgroundColor: '#94a3b8',
            borderWidth: 2,
            borderDash: [6, 4],
            tension: 0.35,
            fill: false,
        },
    ],
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: prefersReducedMotion ? false : { duration: 800 },
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 14, padding: 16, font: { size: 12 } } },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('cs-CZ')} %`,
            },
        },
    },
    scales: {
        y: {
            ticks: { callback: (v) => v + ' %', font: { size: 11 } },
            grid: { color: 'rgba(226, 232, 240, 0.6)' },
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 11 } },
        },
    },
}
</script>

<template>
    <div class="h-72">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
