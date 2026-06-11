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
    Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler)

const props = defineProps({
    years: { type: Array, required: true },
    averages: { type: Array, required: true },
})

const prefersReducedMotion =
    typeof window !== 'undefined'
        ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
        : false

const chartData = computed(() => ({
    labels: props.years,
    datasets: [{
        label: 'Průměrný starobní důchod',
        data: props.averages,
        borderColor: '#059669',
        backgroundColor: 'rgba(5, 150, 105, 0.08)',
        pointBackgroundColor: '#059669',
        pointRadius: 4,
        borderWidth: 2.5,
        tension: 0.3,
        fill: true,
    }],
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: prefersReducedMotion ? false : { duration: 700 },
    plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.y.toLocaleString('cs-CZ')} Kč` } },
    },
    scales: {
        y: { ticks: { callback: (v) => v.toLocaleString('cs-CZ') + ' Kč', font: { size: 11 } }, grid: { color: 'rgba(226,232,240,0.6)' } },
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
    },
}
</script>

<template>
    <div class="h-56">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
