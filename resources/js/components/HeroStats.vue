<script setup>
import { ref, onMounted } from 'vue'

const prefersReducedMotion =
    typeof window !== 'undefined'
        ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
        : false

const STATS = [
    {
        label: 'Průměrný starobní důchod',
        target: 21400,
        suffix: ' Kč',
        trend: '↑ +358 Kč od ledna 2026',
        trendClass: 'text-emerald-600',
    },
    {
        label: 'Valorizace 2026',
        prefix: '+',
        target: 358,
        suffix: ' Kč',
        trend: 'nárůst oproti roku 2025',
        trendClass: 'text-emerald-600',
    },
    {
        label: 'Důchodců v ČR',
        target: 3100000,
        formatMil: true,
        trend: 'starobní, invalidní, pozůstalostní',
        trendClass: 'text-slate-400',
    },
]

const displays = ref(STATS.map(() => '–'))

function fmt(value, stat) {
    if (stat.formatMil) {
        return (value / 1_000_000).toFixed(1).replace('.', ',') + ' mil.'
    }
    return (stat.prefix ?? '') + Math.round(value).toLocaleString('cs-CZ') + (stat.suffix ?? '')
}

function animateCounter(idx, target) {
    if (prefersReducedMotion) {
        displays.value[idx] = fmt(target, STATS[idx])
        return
    }
    const duration = 1400
    const t0 = performance.now()
    const tick = (now) => {
        const t = Math.min((now - t0) / duration, 1)
        const eased = 1 - Math.pow(1 - t, 3)
        displays.value[idx] = fmt(target * eased, STATS[idx])
        if (t < 1) requestAnimationFrame(tick)
    }
    requestAnimationFrame(tick)
}

onMounted(() => {
    STATS.forEach((stat, i) => {
        setTimeout(() => animateCounter(i, stat.target), 350 + i * 180)
    })
})
</script>

<template>
    <div class="flex flex-col gap-4">
        <div
            v-for="(stat, i) in STATS"
            :key="i"
            v-motion
            :initial="prefersReducedMotion ? {} : { opacity: 0, x: 32 }"
            :enter="{
                opacity: 1,
                x: 0,
                transition: { duration: 500, delay: i * 160, easing: 'easeOut' },
            }"
            class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm"
        >
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">
                {{ stat.label }}
            </p>
            <p class="text-3xl font-bold text-slate-900 tabular-nums tracking-tight">
                {{ displays[i] }}
            </p>
            <p v-if="stat.trend" class="mt-2 text-xs" :class="stat.trendClass">
                {{ stat.trend }}
            </p>
        </div>
    </div>
</template>
