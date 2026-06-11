<script setup>
import { ref, computed } from 'vue'

// Zákonný roční limit odpočtu pro produkty spoření na stáří (DIP + DPS + životní pojištění dohromady)
const ANNUAL_DEDUCTION_LIMIT = 48000

const monthlyDeposit = ref(2000)
const taxRate = ref(15)
const otherProductsDeduction = ref(0)

const annualDeposit = computed(() => monthlyDeposit.value * 12)

const availableLimit = computed(() =>
    Math.max(ANNUAL_DEDUCTION_LIMIT - otherProductsDeduction.value, 0),
)

const deductible = computed(() => Math.min(annualDeposit.value, availableLimit.value))

const taxSaving = computed(() => Math.round(deductible.value * (taxRate.value / 100)))

function fmtKc(n) {
    return n.toLocaleString('cs-CZ') + ' Kč'
}
</script>

<template>
    <div class="bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Vstupy -->
                <div class="space-y-6">
                    <div>
                        <div class="flex items-baseline justify-between mb-2">
                            <label for="dip-deposit" class="text-sm font-medium text-slate-700">Měsíční vklad do DIP</label>
                            <span class="text-xl font-bold text-slate-900 tabular-nums">{{ fmtKc(monthlyDeposit) }}</span>
                        </div>
                        <input
                            id="dip-deposit" type="range" v-model.number="monthlyDeposit"
                            min="100" max="8000" step="100" class="w-full accent-emerald-600"
                        />
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>100 Kč</span><span>8 000 Kč</span>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="text-sm font-medium text-slate-700 mb-2">Vaše sazba daně z příjmů</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                class="flex flex-col items-center border rounded-xl px-3 py-2.5 cursor-pointer transition-all"
                                :class="taxRate === 15 ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'"
                            >
                                <input type="radio" v-model.number="taxRate" :value="15" class="sr-only" />
                                <span class="text-sm font-bold" :class="taxRate === 15 ? 'text-emerald-700' : 'text-slate-700'">15 %</span>
                                <span class="text-xs text-slate-400">základní sazba</span>
                            </label>
                            <label
                                class="flex flex-col items-center border rounded-xl px-3 py-2.5 cursor-pointer transition-all"
                                :class="taxRate === 23 ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'"
                            >
                                <input type="radio" v-model.number="taxRate" :value="23" class="sr-only" />
                                <span class="text-sm font-bold" :class="taxRate === 23 ? 'text-emerald-700' : 'text-slate-700'">23 %</span>
                                <span class="text-xs text-slate-400">příjmy nad ~139 tis./měs</span>
                            </label>
                        </div>
                    </fieldset>

                    <div>
                        <div class="flex items-baseline justify-between mb-2">
                            <label for="dip-other" class="text-sm font-medium text-slate-700">
                                Odpočet jiných produktů
                                <span class="block text-xs font-normal text-slate-400">penzijko, životní pojištění (ročně)</span>
                            </label>
                            <span class="text-base font-bold text-slate-900 tabular-nums">{{ fmtKc(otherProductsDeduction) }}</span>
                        </div>
                        <input
                            id="dip-other" type="range" v-model.number="otherProductsDeduction"
                            min="0" max="48000" step="1000" class="w-full accent-emerald-600"
                        />
                    </div>
                </div>

                <!-- Výsledek -->
                <div class="bg-slate-50 rounded-2xl p-6 flex flex-col justify-center">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Roční daňová úspora</p>
                    <p class="text-4xl font-bold text-emerald-600 tabular-nums mb-6" aria-live="polite">{{ fmtKc(taxSaving) }}</p>

                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Roční vklad do DIP</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">{{ fmtKc(annualDeposit) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Uplatnitelný odpočet</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">{{ fmtKc(deductible) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Zbývající limit (48 000 Kč)</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">{{ fmtKc(Math.max(availableLimit - deductible, 0)) }}</dd>
                        </div>
                    </dl>

                    <p v-if="annualDeposit > availableLimit" class="mt-4 text-xs text-amber-700">
                        Vklady nad limit odpočtu se daňově neuplatní — investují se ale dál a zhodnocují.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border-t border-amber-100 px-6 sm:px-8 py-3">
            <p class="text-xs text-amber-700">
                <strong>Orientační výpočet.</strong> Limit 48 000 Kč ročně je společný pro DIP,
                penzijní spoření a životní pojištění. Při výběru před 60. rokem věku (nebo dříve
                než po 10 letech) se uplatněné odpočty dodaňují.
            </p>
        </div>
    </div>
</template>
