<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { calcRetirementAge, calcRetirementDate } from '../../utils/retirementAge.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const AVERAGE_PENSION_2026 = 21400

const prefersReducedMotion =
    typeof window !== 'undefined'
        ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
        : false

// ─── State ───
const step = ref(1)
const STEPS = ['Základní údaje', 'Příjmy', 'Doba pojištění', 'Výsledek']

const birthDate = ref('')
const gender = ref('')
const children = ref(0)
const retirementDate = ref('')
const retirementDateTouched = ref(false)

const incomeMode = ref('average')
const avgIncome = ref(48833)
const yearlyIncome = ref({})
const importText = ref('')
const importMessage = ref('')

const insuranceYears = ref(40)
const subMaternity = ref(false)
const subMaternityYears = ref(3)
const subUnemployment = ref(false)
const subUnemploymentYears = ref(1)
const subStudy = ref(false)
const subStudyYears = ref(1)

const loading = ref(false)
const serverError = ref('')
const result = ref(null)
const animatedTotal = ref(0)
const saveState = ref('idle') // idle | saving | saved | error
const shareCopied = ref(false)

// ─── Krok 1: live preview důchodového věku ───
const normalRetirement = computed(() => {
    if (!birthDate.value || !gender.value) return null
    const bd = new Date(birthDate.value + 'T00:00:00')
    if (isNaN(bd.getTime())) return null
    const yr = bd.getFullYear()
    if (yr < 1936 || yr > 2010) return null

    const age = calcRetirementAge(yr, gender.value, Number(children.value))
    const date = calcRetirementDate(bd, gender.value, Number(children.value))
    return { age, date }
})

const normalAgeLabel = computed(() => {
    if (!normalRetirement.value) return ''
    const { years, months } = normalRetirement.value.age
    return years + ' ' + pluralYears(years) + (months > 0 ? ` a ${months} ${pluralMonths(months)}` : '')
})

const normalDateLabel = computed(() =>
    normalRetirement.value
        ? normalRetirement.value.date.toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric', year: 'numeric' })
        : '',
)

// Default plánovaného odchodu = řádný důchodový věk (dokud uživatel needituje)
watch(normalRetirement, (nr) => {
    if (nr && !retirementDateTouched.value) {
        retirementDate.value = toISODate(nr.date)
    }
})

// ─── Přesluhování / předčasnost ───
const overworkInfo = computed(() => {
    if (!normalRetirement.value || !retirementDate.value) return null
    const planned = new Date(retirementDate.value + 'T00:00:00')
    const normal = normalRetirement.value.date
    const diffDays = Math.round((planned - normal) / 86_400_000)

    if (diffDays >= 90) {
        const periods = Math.floor(diffDays / 90)
        return { type: 'overwork', text: `Přesluhujete ${Math.floor(diffDays / 30)} měsíců po důchodovém věku — procentní výměra se zvýší o ${(periods * 1.5).toLocaleString('cs-CZ')} % výpočtového základu.` }
    }
    if (diffDays <= -90) {
        const periods = Math.ceil(-diffDays / 90)
        return { type: 'early', text: `Plánujete předčasný odchod o ${Math.floor(-diffDays / 30)} měsíců — procentní výměra se sníží o ${(periods * 1.5).toLocaleString('cs-CZ')} % výpočtového základu.` }
    }
    return null
})

// ─── Krok 2: roky pro tabulku příjmů ───
const incomeYearsRange = computed(() => {
    if (!birthDate.value || !retirementDate.value) return []
    const birthYear = new Date(birthDate.value).getFullYear()
    const endYear = new Date(retirementDate.value).getFullYear() - 1
    const startYear = Math.max(1986, birthYear + 18)
    if (endYear < startYear) return []
    const years = []
    for (let y = startYear; y <= Math.min(endYear, 2025); y++) years.push(y)
    return years
})

function importYearlyIncome() {
    const parsed = {}
    let count = 0
    for (const line of importText.value.split('\n')) {
        const match = line.trim().match(/^(\d{4})[\s;,\t]+([\d\s]+)/)
        if (!match) continue
        const year = parseInt(match[1])
        const amount = parseInt(match[2].replace(/\s/g, ''))
        if (year >= 1986 && year <= 2025 && amount >= 0) {
            parsed[year] = amount
            count++
        }
    }
    if (count > 0) {
        yearlyIncome.value = { ...yearlyIncome.value, ...parsed }
        importMessage.value = `Načteno ${count} ${count === 1 ? 'záznam' : count <= 4 ? 'záznamy' : 'záznamů'}.`
    } else {
        importMessage.value = 'Nepodařilo se rozpoznat žádný řádek. Formát: „2020 540000" (rok a roční hrubý příjem).'
    }
}

// ─── Krok 3: celková doba pojištění ───
const totalInsuranceYears = computed(() => {
    let total = Number(insuranceYears.value)
    if (subMaternity.value) total += Number(subMaternityYears.value)
    if (subUnemployment.value) total += Number(subUnemploymentYears.value)
    if (subStudy.value) total += Number(subStudyYears.value)
    return Math.min(total, 55)
})

// ─── Validace kroků ───
const stepValid = computed(() => {
    if (step.value === 1) {
        return Boolean(birthDate.value && gender.value && retirementDate.value
            && new Date(retirementDate.value) > new Date(birthDate.value))
    }
    if (step.value === 2) {
        if (incomeMode.value === 'average') return avgIncome.value > 0
        return Object.values(yearlyIncome.value).some((v) => Number(v) > 0)
    }
    if (step.value === 3) {
        return totalInsuranceYears.value >= 1
    }
    return true
})

function nextStep() {
    if (!stepValid.value) return
    if (step.value === 3) {
        step.value = 4
        calculate()
        return
    }
    step.value++
}

function prevStep() {
    if (step.value > 1) step.value--
    serverError.value = ''
}

// ─── Výpočet (server) ───
function buildPayload() {
    const payload = {
        birth_date: birthDate.value,
        gender: gender.value,
        children: Number(children.value),
        retirement_date: retirementDate.value,
        insurance_years: totalInsuranceYears.value,
        income_mode: incomeMode.value,
        excluded_days: 0,
    }
    if (incomeMode.value === 'average') {
        payload.average_monthly_income = Number(avgIncome.value)
    } else {
        payload.yearly_income = Object.fromEntries(
            Object.entries(yearlyIncome.value).filter(([, v]) => Number(v) > 0),
        )
    }
    return payload
}

async function calculate() {
    loading.value = true
    serverError.value = ''
    result.value = null
    saveState.value = 'idle'

    try {
        const { data } = await window.axios.post('/kalkulacka/vyse/spocitat', buildPayload())
        result.value = data
        syncUrl()
        await nextTick()
        animateTotal(data.totalMonthly)
    } catch (err) {
        if (err.response?.status === 422) {
            const errors = err.response.data?.errors ?? {}
            serverError.value = Object.values(errors).flat().join(' ') || 'Zkontrolujte prosím zadané údaje.'
        } else {
            serverError.value = 'Výpočet se nepodařilo provést. Zkuste to prosím znovu.'
        }
    } finally {
        loading.value = false
    }
}

function animateTotal(target) {
    if (prefersReducedMotion) {
        animatedTotal.value = target
        return
    }
    const duration = 1300
    const t0 = performance.now()
    const tick = (now) => {
        const t = Math.min((now - t0) / duration, 1)
        animatedTotal.value = Math.round(target * (1 - Math.pow(1 - t, 3)))
        if (t < 1) requestAnimationFrame(tick)
    }
    requestAnimationFrame(tick)
}

// ─── URL state (sdílitelné parametry) ───
function syncUrl() {
    const params = new URLSearchParams({
        narozen: birthDate.value,
        pohlavi: gender.value,
        deti: String(children.value),
        odchod: retirementDate.value,
        pojisteni: String(totalInsuranceYears.value),
        prijem: incomeMode.value === 'average' ? String(avgIncome.value) : 'rocne',
    })
    history.replaceState(null, '', location.pathname + '?' + params.toString())
}

function restoreFromUrl() {
    const q = new URLSearchParams(location.search)
    if (!q.has('narozen')) return false

    birthDate.value = q.get('narozen') ?? ''
    gender.value = q.get('pohlavi') ?? ''
    children.value = parseInt(q.get('deti') ?? '0') || 0
    if (q.get('odchod')) {
        retirementDate.value = q.get('odchod')
        retirementDateTouched.value = true
    }
    insuranceYears.value = Math.min(parseInt(q.get('pojisteni') ?? '40') || 40, 50)
    const prijem = q.get('prijem')
    if (prijem && prijem !== 'rocne') {
        avgIncome.value = parseInt(prijem) || 48833
    }
    return Boolean(birthDate.value && gender.value && retirementDate.value)
}

// ─── Akce na výsledku ───
async function saveResult() {
    if (!result.value || saveState.value === 'saving') return
    saveState.value = 'saving'
    try {
        await window.axios.post('/kalkulacka/vyse/ulozit', {
            input_params: buildPayload(),
            result: result.value,
        })
        saveState.value = 'saved'
    } catch {
        saveState.value = 'error'
    }
}

async function shareResult() {
    try {
        await navigator.clipboard.writeText(location.href)
        shareCopied.value = true
        setTimeout(() => (shareCopied.value = false), 2500)
    } catch {
        prompt('Zkopírujte odkaz:', location.href)
    }
}

const pdfLoading = ref(false)

async function downloadPdf() {
    if (pdfLoading.value) return
    pdfLoading.value = true
    try {
        const response = await window.axios.post('/kalkulacka/vyse/pdf', buildPayload(), {
            responseType: 'blob',
        })
        const url = URL.createObjectURL(response.data)
        const link = document.createElement('a')
        link.href = url
        link.download = 'vypocet-duchodu-duchody-cz.pdf'
        link.click()
        URL.revokeObjectURL(url)
    } catch {
        window.print()
    } finally {
        pdfLoading.value = false
    }
}

// ─── Pie chart ───
const chartData = computed(() => {
    if (!result.value) return null
    return {
        labels: result.value.breakdown.map((b) => b.label),
        datasets: [{
            data: result.value.breakdown.map((b) => b.amount),
            backgroundColor: ['#059669', '#34d399', '#fbbf24'],
            borderColor: '#ffffff',
            borderWidth: 2,
        }],
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: prefersReducedMotion ? false : { duration: 900 },
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 12 } } },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${ctx.label}: ${fmtKc(ctx.parsed)}`,
            },
        },
    },
}

// ─── Srovnání s průměrem ───
const comparisonBars = computed(() => {
    if (!result.value) return null
    const max = Math.max(result.value.totalMonthly, AVERAGE_PENSION_2026)
    return {
        user: Math.round((result.value.totalMonthly / max) * 100),
        avg: Math.round((AVERAGE_PENSION_2026 / max) * 100),
        diff: result.value.totalMonthly - AVERAGE_PENSION_2026,
    }
})

// ─── Helpers ───
function toISODate(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0')
}
function fmtKc(n) {
    return Math.round(n).toLocaleString('cs-CZ') + ' Kč'
}
function pluralYears(n) { return n === 1 ? 'rok' : n <= 4 ? 'roky' : 'let' }
function pluralMonths(n) { return n === 1 ? 'měsíc' : n <= 4 ? 'měsíce' : 'měsíců' }

onMounted(() => {
    const restored = restoreFromUrl()
    if (restored && new URLSearchParams(location.search).get('auto') === '1') {
        step.value = 4
        calculate()
    }
})
</script>

<template>
    <div class="bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden">

        <!-- ─── Progress bar ─── -->
        <div class="px-6 sm:px-8 pt-6 print:hidden">
            <div class="flex items-center justify-between mb-3">
                <ol class="flex items-center gap-0 w-full" role="list">
                    <li v-for="(label, i) in STEPS" :key="i" class="flex items-center" :class="i < STEPS.length - 1 ? 'flex-1' : ''">
                        <button
                            type="button"
                            class="flex items-center gap-2 group"
                            :disabled="i + 1 >= step"
                            :aria-current="step === i + 1 ? 'step' : undefined"
                            @click="i + 1 < step && (step = i + 1)"
                        >
                            <span
                                class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                                :class="step > i + 1
                                    ? 'bg-emerald-600 text-white'
                                    : step === i + 1
                                        ? 'bg-emerald-600 text-white ring-4 ring-emerald-100'
                                        : 'bg-slate-100 text-slate-400'"
                            >
                                <svg v-if="step > i + 1" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <template v-else>{{ i + 1 }}</template>
                            </span>
                            <span
                                class="hidden md:block text-xs font-medium whitespace-nowrap"
                                :class="step >= i + 1 ? 'text-slate-900' : 'text-slate-400'"
                            >{{ label }}</span>
                        </button>
                        <div v-if="i < STEPS.length - 1" class="flex-1 h-px mx-3" :class="step > i + 1 ? 'bg-emerald-300' : 'bg-slate-200'"></div>
                    </li>
                </ol>
            </div>
        </div>

        <div class="p-6 sm:p-8">

            <!-- ═══ KROK 1: Základní údaje ═══ -->
            <div v-show="step === 1" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label for="pc-birth" class="block text-sm font-medium text-slate-700">
                            Datum narození <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="pc-birth" type="date" v-model="birthDate"
                            min="1936-01-02" max="2010-12-31"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                        />
                    </div>

                    <fieldset class="space-y-1.5">
                        <legend class="block text-sm font-medium text-slate-700 mb-1.5">
                            Pohlaví <span class="text-red-500" aria-hidden="true">*</span>
                        </legend>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                class="flex items-center justify-center gap-2 border rounded-xl px-3 py-2.5 text-sm font-medium cursor-pointer transition-all"
                                :class="gender === 'M' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'"
                            >
                                <input type="radio" v-model="gender" value="M" class="sr-only" />
                                Muž
                            </label>
                            <label
                                class="flex items-center justify-center gap-2 border rounded-xl px-3 py-2.5 text-sm font-medium cursor-pointer transition-all"
                                :class="gender === 'F' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'"
                            >
                                <input type="radio" v-model="gender" value="F" class="sr-only" />
                                Žena
                            </label>
                        </div>
                    </fieldset>
                </div>

                <div class="space-y-1.5">
                    <label for="pc-children" class="block text-sm font-medium text-slate-700">
                        Počet vychovaných dětí — <span class="font-bold text-emerald-700">{{ children >= 6 ? '6+' : children }}</span>
                    </label>
                    <input
                        id="pc-children" type="range" v-model.number="children" min="0" max="6" step="1"
                        class="w-full accent-emerald-600"
                    />
                    <p class="text-xs text-slate-500">Výchovné +500 Kč/měsíc za dítě. U žen narozených do r. 1965 snižuje i důchodový věk.</p>
                </div>

                <div class="space-y-1.5">
                    <label for="pc-retirement" class="block text-sm font-medium text-slate-700">
                        Plánované datum odchodu do důchodu
                    </label>
                    <input
                        id="pc-retirement" type="date" v-model="retirementDate"
                        @input="retirementDateTouched = true"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                    />
                    <p class="text-xs text-slate-500">Předvyplněno na řádný důchodový věk — můžete změnit pro předčasný odchod nebo přesluhování.</p>
                </div>

                <!-- Live preview -->
                <div v-if="normalRetirement" class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4" aria-live="polite">
                    <p class="text-sm text-emerald-800">
                        <strong>Váš důchodový věk: {{ normalAgeLabel }}</strong>
                        <span class="text-emerald-700"> ({{ normalDateLabel }})</span>
                    </p>
                </div>

                <div v-if="overworkInfo" class="border-l-4 rounded-r-xl px-4 py-3"
                     :class="overworkInfo.type === 'overwork' ? 'border-emerald-400 bg-emerald-50' : 'border-amber-400 bg-amber-50'">
                    <p class="text-xs" :class="overworkInfo.type === 'overwork' ? 'text-emerald-800' : 'text-amber-800'">
                        {{ overworkInfo.text }}
                    </p>
                </div>
            </div>

            <!-- ═══ KROK 2: Příjmy ═══ -->
            <div v-show="step === 2" class="space-y-6">
                <!-- Toggle -->
                <div class="grid grid-cols-2 gap-2 bg-slate-100 rounded-xl p-1" role="tablist">
                    <button
                        type="button" role="tab" :aria-selected="incomeMode === 'average'"
                        class="rounded-lg px-4 py-2.5 text-sm font-semibold transition-all"
                        :class="incomeMode === 'average' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        @click="incomeMode = 'average'"
                    >Průměrný plat</button>
                    <button
                        type="button" role="tab" :aria-selected="incomeMode === 'yearly'"
                        class="rounded-lg px-4 py-2.5 text-sm font-semibold transition-all"
                        :class="incomeMode === 'yearly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        @click="incomeMode = 'yearly'"
                    >Rok po roku</button>
                </div>

                <!-- Average -->
                <div v-if="incomeMode === 'average'" class="space-y-4">
                    <div>
                        <div class="flex items-baseline justify-between mb-2">
                            <label for="pc-income" class="text-sm font-medium text-slate-700">Průměrný hrubý měsíční příjem</label>
                            <span class="text-2xl font-bold text-slate-900 tabular-nums">{{ fmtKc(avgIncome) }}</span>
                        </div>
                        <input
                            id="pc-income" type="range" v-model.number="avgIncome"
                            min="10000" max="100000" step="500"
                            class="w-full accent-emerald-600"
                        />
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>10 000 Kč</span>
                            <span>100 000 Kč</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <label for="pc-income-exact" class="text-sm text-slate-600 shrink-0">Přesná částka:</label>
                        <input
                            id="pc-income-exact" type="number" v-model.number="avgIncome" min="0" max="9999999"
                            class="w-36 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                        />
                        <span class="text-sm text-slate-400">Kč/měsíc</span>
                    </div>
                </div>

                <!-- Yearly -->
                <div v-else class="space-y-5">
                    <div class="space-y-2">
                        <label for="pc-import" class="block text-sm font-medium text-slate-700">Hromadný import</label>
                        <textarea
                            id="pc-import" v-model="importText" rows="4"
                            placeholder="2018 480000&#10;2019 510000&#10;2020 540000"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                        ></textarea>
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors"
                                @click="importYearlyIncome"
                            >Načíst řádky →</button>
                            <span v-if="importMessage" class="text-xs text-slate-500">{{ importMessage }}</span>
                        </div>
                    </div>

                    <div v-if="incomeYearsRange.length" class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-2.5">
                            <div v-for="year in incomeYearsRange" :key="year" class="flex items-center gap-2">
                                <label :for="`pc-y-${year}`" class="text-xs font-medium text-slate-500 w-10 shrink-0 tabular-nums">{{ year }}</label>
                                <input
                                    :id="`pc-y-${year}`" type="number" min="0" step="1000"
                                    v-model.number="yearlyIncome[year]"
                                    placeholder="0"
                                    class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 tabular-nums focus:border-emerald-500 outline-none transition-colors"
                                />
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-3">Roční hrubý příjem v Kč. Roky bez příjmu nechte prázdné.</p>
                    </div>
                    <p v-else class="text-sm text-slate-400">Nejdříve vyplňte datum narození a odchodu v kroku 1.</p>
                </div>

                <div class="border-l-4 border-blue-300 bg-blue-50 rounded-r-xl px-4 py-3">
                    <p class="text-xs text-blue-800">
                        <strong>Tip:</strong> Přesné příjmy najdete v <em>Informativním osobním listu důchodového pojištění</em> — zdarma na ePortálu ČSSZ.
                    </p>
                </div>
            </div>

            <!-- ═══ KROK 3: Doba pojištění ═══ -->
            <div v-show="step === 3" class="space-y-6">
                <div>
                    <div class="flex items-baseline justify-between mb-2">
                        <label for="pc-insurance" class="text-sm font-medium text-slate-700">Doba pojištění (odpracované roky)</label>
                        <span class="text-2xl font-bold text-slate-900 tabular-nums">{{ insuranceYears }} {{ pluralYears(insuranceYears) }}</span>
                    </div>
                    <input
                        id="pc-insurance" type="range" v-model.number="insuranceYears" min="0" max="50" step="1"
                        class="w-full accent-emerald-600"
                    />
                    <div class="flex justify-between text-xs text-slate-400 mt-1">
                        <span>0</span>
                        <span>50 let</span>
                    </div>
                </div>

                <fieldset class="space-y-3">
                    <legend class="text-sm font-medium text-slate-700 mb-2">Náhradní doby pojištění</legend>

                    <div class="border border-slate-200 rounded-xl px-4 py-3 flex items-center gap-3 flex-wrap">
                        <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-48">
                            <input type="checkbox" v-model="subMaternity" class="rounded text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-slate-700">Mateřská / rodičovská</span>
                        </label>
                        <div v-if="subMaternity" class="flex items-center gap-2">
                            <input type="number" v-model.number="subMaternityYears" min="0" max="12"
                                   class="w-16 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center tabular-nums focus:border-emerald-500 outline-none" />
                            <span class="text-xs text-slate-400">let</span>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-xl px-4 py-3 flex items-center gap-3 flex-wrap">
                        <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-48">
                            <input type="checkbox" v-model="subUnemployment" class="rounded text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-slate-700">Evidence na úřadu práce</span>
                        </label>
                        <div v-if="subUnemployment" class="flex items-center gap-2">
                            <input type="number" v-model.number="subUnemploymentYears" min="0" max="3"
                                   class="w-16 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center tabular-nums focus:border-emerald-500 outline-none" />
                            <span class="text-xs text-slate-400">let (max 3)</span>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-xl px-4 py-3 flex items-center gap-3 flex-wrap">
                        <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-48">
                            <input type="checkbox" v-model="subStudy" class="rounded text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-slate-700">Studium před rokem 2010</span>
                        </label>
                        <div v-if="subStudy" class="flex items-center gap-2">
                            <input type="number" v-model.number="subStudyYears" min="0" max="6"
                                   class="w-16 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center tabular-nums focus:border-emerald-500 outline-none" />
                            <span class="text-xs text-slate-400">let</span>
                        </div>
                    </div>
                </fieldset>

                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-600">Celková doba pojištění</span>
                    <span class="text-xl font-bold text-slate-900 tabular-nums">{{ totalInsuranceYears }} {{ pluralYears(totalInsuranceYears) }}</span>
                </div>

                <div v-if="overworkInfo?.type === 'overwork'" class="border-l-4 border-emerald-400 bg-emerald-50 rounded-r-xl px-4 py-3">
                    <p class="text-xs text-emerald-800">{{ overworkInfo.text }}</p>
                </div>
            </div>

            <!-- ═══ KROK 4: Výsledek ═══ -->
            <div v-show="step === 4">
                <!-- Loading -->
                <div v-if="loading" class="py-16 text-center" aria-live="polite">
                    <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p class="text-sm text-slate-500">Počítáme váš důchod…</p>
                </div>

                <!-- Error -->
                <div v-else-if="serverError" class="py-8">
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-4 mb-6">
                        {{ serverError }}
                    </div>
                    <button type="button" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700" @click="step = 1">
                        ← Zkontrolovat zadání
                    </button>
                </div>

                <!-- Result -->
                <div v-else-if="result" class="space-y-8">
                    <!-- Big number -->
                    <div class="text-center pt-2">
                        <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-3">Váš odhadovaný důchod</p>
                        <p class="text-5xl sm:text-6xl font-bold text-slate-900 tabular-nums tracking-tight" aria-live="polite">
                            {{ animatedTotal.toLocaleString('cs-CZ') }} <span class="text-3xl sm:text-4xl text-slate-400 font-semibold">Kč/měsíc</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Breakdown -->
                        <div class="border border-slate-200 rounded-2xl p-6">
                            <h3 class="text-sm font-semibold text-slate-900 mb-4">Složení důchodu</h3>
                            <dl class="space-y-3">
                                <div v-for="(item, i) in result.breakdown" :key="i" class="flex items-center justify-between gap-4">
                                    <dt class="text-sm text-slate-500">{{ item.label }}</dt>
                                    <dd class="text-sm font-semibold text-slate-900 tabular-nums whitespace-nowrap">{{ fmtKc(item.amount) }}</dd>
                                </div>
                                <div class="border-t border-slate-200 pt-3 flex items-center justify-between gap-4">
                                    <dt class="text-sm font-bold text-slate-900">Celkem měsíčně</dt>
                                    <dd class="text-base font-bold text-emerald-600 tabular-nums whitespace-nowrap">{{ fmtKc(result.totalMonthly) }}</dd>
                                </div>
                            </dl>

                            <div class="mt-5 pt-4 border-t border-slate-100 space-y-1.5">
                                <p class="text-xs text-slate-400">Osobní vyměřovací základ: <span class="tabular-nums font-medium text-slate-500">{{ fmtKc(result.personalAssessmentBase) }}</span></p>
                                <p class="text-xs text-slate-400">Výpočtový základ po redukci: <span class="tabular-nums font-medium text-slate-500">{{ fmtKc(result.calculationBase) }}</span></p>
                            </div>
                        </div>

                        <!-- Pie chart -->
                        <div class="border border-slate-200 rounded-2xl p-6">
                            <h3 class="text-sm font-semibold text-slate-900 mb-4">Vizuální podíl složek</h3>
                            <div class="h-64">
                                <Pie v-if="chartData" :data="chartData" :options="chartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Srovnání s průměrem -->
                    <div v-if="comparisonBars" class="border border-slate-200 rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Srovnání s průměrným důchodem</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-xs mb-1.5">
                                    <span class="font-medium text-slate-700">Váš odhad</span>
                                    <span class="font-semibold text-slate-900 tabular-nums">{{ fmtKc(result.totalMonthly) }}</span>
                                </div>
                                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-700" :style="{ width: comparisonBars.user + '%' }"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-1.5">
                                    <span class="font-medium text-slate-700">Průměr ČR (2026)</span>
                                    <span class="font-semibold text-slate-900 tabular-nums">{{ fmtKc(AVERAGE_PENSION_2026) }}</span>
                                </div>
                                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-slate-400 rounded-full transition-all duration-700" :style="{ width: comparisonBars.avg + '%' }"></div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-4 text-xs" :class="comparisonBars.diff >= 0 ? 'text-emerald-700' : 'text-amber-700'">
                            <template v-if="comparisonBars.diff >= 0">
                                Váš odhad je o {{ fmtKc(comparisonBars.diff) }} nad průměrným starobním důchodem.
                            </template>
                            <template v-else>
                                Váš odhad je o {{ fmtKc(-comparisonBars.diff) }} pod průměrným starobním důchodem.
                            </template>
                        </p>
                    </div>

                    <!-- Akce -->
                    <div class="flex flex-wrap gap-3 print:hidden">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 border border-slate-300 hover:border-emerald-300 hover:text-emerald-700 text-slate-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition-all"
                            :disabled="saveState === 'saving' || saveState === 'saved'"
                            @click="saveResult"
                        >
                            <svg v-if="saveState !== 'saved'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <svg v-else class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ saveState === 'saved' ? 'Uloženo' : saveState === 'saving' ? 'Ukládám…' : saveState === 'error' ? 'Zkusit znovu' : 'Uložit výsledek' }}
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 border border-slate-300 hover:border-emerald-300 hover:text-emerald-700 text-slate-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition-all"
                            @click="shareResult"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            {{ shareCopied ? 'Odkaz zkopírován ✓' : 'Sdílet' }}
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 border border-slate-300 hover:border-emerald-300 hover:text-emerald-700 text-slate-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition-all disabled:opacity-50"
                            :disabled="pdfLoading"
                            @click="downloadPdf"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ pdfLoading ? 'Generuji…' : 'Stáhnout PDF' }}
                        </button>
                    </div>

                    <!-- CTA -->
                    <a
                        href="/fondy"
                        class="flex items-center justify-between gap-4 bg-slate-900 hover:bg-slate-800 rounded-2xl px-6 py-5 transition-colors group print:hidden"
                    >
                        <div>
                            <p class="text-white font-semibold mb-0.5">Jak zvýšit svůj důchod?</p>
                            <p class="text-sm text-slate-400">Srovnejte penzijní fondy a doplňkové spoření — i 500 Kč měsíčně dělá rozdíl.</p>
                        </div>
                        <svg class="h-5 w-5 text-emerald-400 shrink-0 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- ─── Navigace ─── -->
            <div v-if="step < 4 || serverError" class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between print:hidden">
                <button
                    v-if="step > 1"
                    type="button"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors"
                    @click="prevStep"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Zpět
                </button>
                <span v-else></span>

                <button
                    v-if="step < 4"
                    type="button"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white text-sm font-semibold px-6 py-3 rounded-xl shadow-sm transition-all"
                    :disabled="!stepValid"
                    @click="nextStep"
                >
                    {{ step === 3 ? 'Spočítat důchod' : 'Pokračovat' }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
