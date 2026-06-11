<script setup>
import { ref, onMounted } from 'vue'

const prefersReducedMotion =
    typeof window !== 'undefined'
        ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
        : false

const STATS = [
    { label: 'důchodců v ČR', target: 3_100_000, formatMil: true },
    { label: 'průměrný starobní důchod', target: 21_400, suffix: ' Kč' },
    { label: 'základní výměra 2026', target: 4_900, suffix: ' Kč' },
    { label: 'průměrný věk odchodu', target: 64, suffix: ' let' },
]

const displays = ref(STATS.map(() => '0'))
const rootEl = ref(null)
const started = ref(false)

function fmt(value, stat) {
    if (stat.formatMil) {
        return (value / 1_000_000).toFixed(1).replace('.', ',') + ' mil.'
    }
    return Math.round(value).toLocaleString('cs-CZ') + (stat.suffix ?? '')
}

function animateCounter(idx) {
    const stat = STATS[idx]
    if (prefersReducedMotion) {
        displays.value[idx] = fmt(stat.target, stat)
        return
    }
    const duration = 1600
    const t0 = performance.now()
    const tick = (now) => {
        const t = Math.min((now - t0) / duration, 1)
        const eased = 1 - Math.pow(1 - t, 3)
        displays.value[idx] = fmt(stat.target * eased, stat)
        if (t < 1) requestAnimationFrame(tick)
    }
    requestAnimationFrame(tick)
}

function startAll() {
    STATS.forEach((_, i) => setTimeout(() => animateCounter(i), i * 120))
}

onMounted(() => {
    if (prefersReducedMotion) { startAll(); return }

    const observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting && !started.value) {
                started.value = true
                startAll()
                observer.disconnect()
            }
        },
        { threshold: 0.25 },
    )
    if (rootEl.value) observer.observe(rootEl.value)
})
</script>

<template>
    <div ref="rootEl">
        <div class="text-center mb-14">
            <p class="text-xs font-semibold text-emerald-400 uppercase tracking-widest mb-3">
                Česko v číslech
            </p>
            <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight mb-3">
                Důchodový systém v ČR
            </h2>
            <p class="text-slate-400 max-w-md mx-auto">
                Aktuální statistiky k roku 2026 — průběžně aktualizováno z dat ČSSZ a MPSV.
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            <div
                v-for="(stat, i) in STATS"
                :key="i"
                v-motion
                :initial="prefersReducedMotion ? {} : { opacity: 0, y: 28 }"
                :visible-once="{
                    opacity: 1,
                    y: 0,
                    transition: { duration: 550, delay: i * 100, easing: 'easeOut' },
                }"
                class="text-center"
            >
                <p class="text-4xl sm:text-5xl font-bold text-white tabular-nums tracking-tight">
                    {{ displays[i] }}
                </p>
                <p class="mt-2.5 text-sm text-slate-400 leading-snug">{{ stat.label }}</p>
            </div>
        </div>
    </div>
</template>
