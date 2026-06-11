<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    funds: { type: Array, required: true },
})

const monthlyDeposit = ref(1700)
const years = ref(20)
const selectedSlug = ref(props.funds[0]?.slug ?? '')

const selectedFund = computed(() =>
    props.funds.find((f) => f.slug === selectedSlug.value) ?? null,
)

// Státní příspěvek dle pravidel od 7/2024: 20 % z úložky 500–1700 Kč, max 340 Kč
const stateContribution = computed(() => {
    if (monthlyDeposit.value < 500) return 0
    return Math.round(Math.min(monthlyDeposit.value, 1700) * 0.2)
})

const result = computed(() => {
    if (!selectedFund.value) return null

    const annualReturn = (selectedFund.value.return5y ?? 0) / 100
    const monthlyRate = annualReturn / 12
    const months = years.value * 12
    const monthlyTotal = monthlyDeposit.value + stateContribution.value

    // Budoucí hodnota pravidelné měsíční úložky (anuita)
    const futureValue = monthlyRate > 0
        ? monthlyTotal * ((Math.pow(1 + monthlyRate, months) - 1) / monthlyRate)
        : monthlyTotal * months

    const ownDeposits = monthlyDeposit.value * months
    const stateTotal = stateContribution.value * months

    return {
        futureValue: Math.round(futureValue),
        ownDeposits,
        stateTotal,
        gain: Math.round(futureValue - ownDeposits - stateTotal),
    }
})

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
                            <label for="sc-deposit" class="text-sm font-medium text-slate-700">Měsíční úložka</label>
                            <span class="text-xl font-bold text-slate-900 tabular-nums">{{ fmtKc(monthlyDeposit) }}</span>
                        </div>
                        <input
                            id="sc-deposit" type="range" v-model.number="monthlyDeposit"
                            min="100" max="5000" step="100" class="w-full accent-emerald-600"
                        />
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>100 Kč</span><span>5 000 Kč</span>
                        </div>
                        <p v-if="stateContribution > 0" class="mt-2 text-xs text-emerald-700">
                            + státní příspěvek {{ fmtKc(stateContribution) }}/měsíc
                        </p>
                        <p v-else class="mt-2 text-xs text-amber-700">
                            Státní příspěvek získáte od úložky 500 Kč.
                        </p>
                    </div>

                    <div>
                        <div class="flex items-baseline justify-between mb-2">
                            <label for="sc-years" class="text-sm font-medium text-slate-700">Doba spoření</label>
                            <span class="text-xl font-bold text-slate-900 tabular-nums">{{ years }} {{ years === 1 ? 'rok' : years <= 4 ? 'roky' : 'let' }}</span>
                        </div>
                        <input
                            id="sc-years" type="range" v-model.number="years"
                            min="1" max="40" step="1" class="w-full accent-emerald-600"
                        />
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>1 rok</span><span>40 let</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="sc-fund" class="block text-sm font-medium text-slate-700">Fond</label>
                        <select
                            id="sc-fund" v-model="selectedSlug"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                        >
                            <option v-for="fund in funds" :key="fund.slug" :value="fund.slug">
                                {{ fund.name }} ({{ (fund.return5y ?? 0).toLocaleString('cs-CZ') }} % p.a.)
                            </option>
                        </select>
                        <p class="text-xs text-slate-400">Počítáme s průměrným výnosem fondu za posledních 5 let.</p>
                    </div>
                </div>

                <!-- Výsledek -->
                <div v-if="result" class="bg-slate-50 rounded-2xl p-6 flex flex-col justify-center">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Naspořeno za {{ years }} {{ years === 1 ? 'rok' : years <= 4 ? 'roky' : 'let' }}</p>
                    <p class="text-4xl font-bold text-slate-900 tabular-nums mb-6" aria-live="polite">{{ fmtKc(result.futureValue) }}</p>

                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Vaše vklady</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">{{ fmtKc(result.ownDeposits) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Státní příspěvky</dt>
                            <dd class="font-semibold text-emerald-600 tabular-nums">+{{ fmtKc(result.stateTotal) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Zhodnocení</dt>
                            <dd class="font-semibold tabular-nums" :class="result.gain >= 0 ? 'text-emerald-600' : 'text-red-600'">
                                {{ result.gain >= 0 ? '+' : '' }}{{ fmtKc(result.gain) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border-t border-amber-100 px-6 sm:px-8 py-3">
            <p class="text-xs text-amber-700">
                <strong>Orientační výpočet.</strong> Vychází z minulých výnosů, které nejsou zárukou
                budoucích. Nezohledňuje daň při výběru ani příspěvek zaměstnavatele.
            </p>
        </div>
    </div>
</template>
