<script setup>
import { ref, computed } from 'vue'
import { calcRetirementAge, calcRetirementDate } from '../utils/retirementAge.js'

const birthDate = ref('')
const gender = ref('')
const children = ref('0')

const result = computed(() => {
    if (!birthDate.value || !gender.value) return null

    const bDate = new Date(birthDate.value + 'T00:00:00')
    if (isNaN(bDate.getTime())) return null

    const yr = bDate.getFullYear()
    if (yr < 1936 || yr > 2005) return null

    const rDate = calcRetirementDate(bDate, gender.value, parseInt(children.value))
    const age = calcRetirementAge(yr, gender.value, parseInt(children.value))

    const today = new Date()
    today.setHours(0, 0, 0, 0)
    rDate.setHours(0, 0, 0, 0)

    const isPast = rDate <= today

    return {
        dateFormatted: rDate.toLocaleDateString('cs-CZ', {
            day: 'numeric',
            month: 'numeric',
            year: 'numeric',
        }),
        isPast,
        remaining: isPast ? null : remaining(today, rDate),
        ageLabel: age.years + ' let' + (age.months > 0 ? ' ' + age.months + ' m.' : ''),
    }
})

function remaining(from, to) {
    let years = to.getFullYear() - from.getFullYear()
    let months = to.getMonth() - from.getMonth()
    let days = to.getDate() - from.getDate()

    if (days < 0) {
        months--
        days += new Date(to.getFullYear(), to.getMonth(), 0).getDate()
    }
    if (months < 0) { years--; months += 12 }

    const parts = []
    if (years > 0) parts.push(years + ' ' + pluralY(years))
    if (months > 0) parts.push(months + ' ' + pluralM(months))
    if (years === 0 && months === 0 && days > 0) parts.push(days + ' ' + pluralD(days))

    return parts.length ? 'za ' + parts.join(' a ') : 'dnes'
}

function pluralY(n) { return n === 1 ? 'rok' : n <= 4 ? 'roky' : 'let' }
function pluralM(n) { return n === 1 ? 'měsíc' : n <= 4 ? 'měsíce' : 'měsíců' }
function pluralD(n) { return n === 1 ? 'den' : n <= 4 ? 'dny' : 'dní' }
</script>

<template>
    <div class="bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 sm:p-8">
            <!-- Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="space-y-1.5">
                    <label for="qc-birth" class="block text-sm font-medium text-slate-700">
                        Datum narození
                    </label>
                    <input
                        id="qc-birth"
                        type="date"
                        v-model="birthDate"
                        min="1936-01-01"
                        max="2005-12-31"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                    />
                </div>

                <div class="space-y-1.5">
                    <label for="qc-gender" class="block text-sm font-medium text-slate-700">
                        Pohlaví
                    </label>
                    <select
                        id="qc-gender"
                        v-model="gender"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                    >
                        <option value="">Vyberte…</option>
                        <option value="M">Muž</option>
                        <option value="F">Žena</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="qc-children" class="block text-sm font-medium text-slate-700">
                        Počet dětí
                        <span class="ml-1 text-xs font-normal text-slate-400">(ženy do r. 1965)</span>
                    </label>
                    <select
                        id="qc-children"
                        v-model="children"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                    >
                        <option value="0">0 – bezdětná/ý</option>
                        <option value="1">1 dítě</option>
                        <option value="2">2 děti</option>
                        <option value="3">3 děti</option>
                        <option value="4">4 a více</option>
                    </select>
                </div>
            </div>

            <!-- Result -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <template v-if="result">
                    <!-- Already retired -->
                    <div v-if="result.isPast" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-900">{{ result.dateFormatted }}</span>
                            <span class="ml-2 text-sm text-slate-500">— důchodový věk byl dosažen</span>
                        </div>
                    </div>

                    <!-- Future -->
                    <div v-else class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Datum odchodu</p>
                            <p class="text-3xl font-bold text-slate-900 tabular-nums">{{ result.dateFormatted }}</p>
                        </div>
                        <div class="hidden sm:block h-12 w-px bg-slate-200 shrink-0"></div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Zbývá</p>
                            <p class="text-xl font-semibold text-slate-900">{{ result.remaining }}</p>
                        </div>
                        <div class="hidden sm:block h-12 w-px bg-slate-200 shrink-0"></div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Důchodový věk</p>
                            <p class="text-xl font-semibold text-slate-900">{{ result.ageLabel }}</p>
                        </div>
                    </div>

                    <a
                        href="/kalkulacka/vyse"
                        class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors"
                    >
                        Spočítat výši důchodu
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </template>

                <p v-else class="text-sm text-slate-400">
                    Výsledek se zobrazí ihned po zadání data narození a pohlaví.
                </p>
            </div>
        </div>

        <div class="bg-amber-50 border-t border-amber-100 px-6 sm:px-8 py-3">
            <p class="text-xs text-amber-700">
                <strong>Orientační výpočet</strong> — přesný termín závisí na splnění podmínek pojištění dle zákona 155/1995&nbsp;Sb.
            </p>
        </div>
    </div>
</template>
