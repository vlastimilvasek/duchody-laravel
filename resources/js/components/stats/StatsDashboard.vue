<script setup>
import { computed } from 'vue'
import { Line, Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Tooltip, Legend, Filler)

const props = defineProps({
    years: { type: Array, required: true },
    averages: { type: Array, required: true },
    counts: { type: Array, required: true },
    distribution: { type: Array, required: true },
    euComparison: { type: Array, required: true },
})

const prefersReducedMotion =
    typeof window !== 'undefined'
        ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
        : false

const baseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: prefersReducedMotion ? false : { duration: 700 },
    plugins: { legend: { display: false } },
}

// ─── 1. Vývoj průměrného důchodu (LineChart) ───
const averageData = computed(() => ({
    labels: props.years,
    datasets: [{
        label: 'Průměrný starobní důchod',
        data: props.averages,
        borderColor: '#059669',
        backgroundColor: 'rgba(5, 150, 105, 0.07)',
        pointBackgroundColor: '#059669',
        pointRadius: 3,
        borderWidth: 2.5,
        tension: 0.3,
        fill: true,
    }],
}))

const averageOptions = {
    ...baseOptions,
    plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.y.toLocaleString('cs-CZ')} Kč` } },
    },
    scales: {
        y: { ticks: { callback: (v) => (v / 1000) + ' tis.', font: { size: 11 } }, grid: { color: 'rgba(226,232,240,0.6)' } },
        x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0, autoSkip: true } },
    },
}

// ─── 2. Počet důchodců (AreaChart) ───
const countData = computed(() => ({
    labels: props.years,
    datasets: [{
        label: 'Počet starobních důchodců',
        data: props.counts,
        borderColor: '#475569',
        backgroundColor: 'rgba(71, 85, 105, 0.12)',
        pointRadius: 0,
        borderWidth: 2,
        tension: 0.3,
        fill: true,
    }],
}))

const countOptions = {
    ...baseOptions,
    plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx) => ` ${(ctx.parsed.y / 1_000_000).toFixed(2).replace('.', ',')} mil. důchodců` } },
    },
    scales: {
        y: {
            min: 2000000,
            ticks: { callback: (v) => (v / 1_000_000).toFixed(1).replace('.', ',') + ' mil.', font: { size: 11 } },
            grid: { color: 'rgba(226,232,240,0.6)' },
        },
        x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0, autoSkip: true } },
    },
}

// ─── 3. Rozložení výše důchodů (Histogram) ───
const distributionData = computed(() => ({
    labels: props.distribution.map((d) => d.range),
    datasets: [{
        label: 'Podíl důchodců',
        data: props.distribution.map((d) => d.share),
        backgroundColor: '#34d399',
        hoverBackgroundColor: '#059669',
        borderRadius: 6,
        barPercentage: 0.85,
        categoryPercentage: 0.9,
    }],
}))

const distributionOptions = {
    ...baseOptions,
    plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.y} % důchodců` } },
    },
    scales: {
        y: { ticks: { callback: (v) => v + ' %', font: { size: 11 } }, grid: { color: 'rgba(226,232,240,0.6)' } },
        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } },
    },
}

// ─── 4. ČR vs EU (BarChart, horizontální) ───
const euData = computed(() => ({
    labels: props.euComparison.map((d) => d.country),
    datasets: [{
        label: 'Náhradový poměr',
        data: props.euComparison.map((d) => d.value),
        backgroundColor: props.euComparison.map((d) =>
            d.country === 'Česko' ? '#059669' : d.country === 'EU průměr' ? '#94a3b8' : '#cbd5e1',
        ),
        borderRadius: 6,
        barPercentage: 0.75,
    }],
}))

const euOptions = {
    ...baseOptions,
    indexAxis: 'y',
    plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.x} % průměrné mzdy` } },
    },
    scales: {
        x: { ticks: { callback: (v) => v + ' %', font: { size: 11 } }, grid: { color: 'rgba(226,232,240,0.6)' } },
        y: { grid: { display: false }, ticks: { font: { size: 12 } } },
    },
}
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="bg-white border border-slate-200 rounded-2xl p-6" aria-label="Vývoj průměrného důchodu">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Vývoj průměrného důchodu</h2>
            <p class="text-xs text-slate-400 mb-5">Starobní důchod {{ years[0] }}–{{ years[years.length - 1] }}, Kč/měsíc</p>
            <div class="h-64"><Line :data="averageData" :options="averageOptions" /></div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl p-6" aria-label="Počet důchodců">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Počet starobních důchodců</h2>
            <p class="text-xs text-slate-400 mb-5">Vývoj {{ years[0] }}–{{ years[years.length - 1] }}</p>
            <div class="h-64"><Line :data="countData" :options="countOptions" /></div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl p-6" aria-label="Rozložení výše důchodů">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Rozložení výše důchodů</h2>
            <p class="text-xs text-slate-400 mb-5">Podíl důchodců dle měsíční výše, Kč (2026)</p>
            <div class="h-64"><Bar :data="distributionData" :options="distributionOptions" /></div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl p-6" aria-label="Srovnání s EU">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Srovnání s Evropou</h2>
            <p class="text-xs text-slate-400 mb-5">Náhradový poměr — důchod jako % průměrné mzdy</p>
            <div class="h-64"><Bar :data="euData" :options="euOptions" /></div>
        </section>
    </div>
</template>
