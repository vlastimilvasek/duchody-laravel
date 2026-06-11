<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    funds: { type: Array, required: true },
})

const TYPE_META = {
    konzervativni: { label: 'Konzervativní', badge: 'bg-blue-50 text-blue-700 border-blue-200' },
    vyvazeny: { label: 'Vyvážený', badge: 'bg-amber-50 text-amber-700 border-amber-200' },
    dynamicky: { label: 'Dynamický', badge: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
}

// ─── Filtry ───
const typeFilter = ref('all')
const search = ref('')

const FILTERS = [
    { value: 'all', label: 'Všechny fondy' },
    { value: 'konzervativni', label: 'Konzervativní' },
    { value: 'vyvazeny', label: 'Vyvážené' },
    { value: 'dynamicky', label: 'Dynamické' },
]

// ─── Řazení ───
const sortKey = ref('return1y')
const sortDir = ref('desc')

const COLUMNS = [
    { key: 'name', label: 'Fond', sortable: true },
    { key: 'fundType', label: 'Typ', sortable: false },
    { key: 'return1y', label: 'Výnos 1 rok', sortable: true },
    { key: 'return3y', label: 'Výnos 3 roky', sortable: true },
    { key: 'return5y', label: 'Výnos 5 let', sortable: true },
    { key: 'feeManagement', label: 'Poplatek', sortable: true },
    { key: 'assetsMil', label: 'Majetek', sortable: true },
]

function toggleSort(key) {
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortKey.value = key
        sortDir.value = key === 'name' ? 'asc' : 'desc'
    }
}

const visibleFunds = computed(() => {
    let list = props.funds

    if (typeFilter.value !== 'all') {
        list = list.filter((f) => f.fundType === typeFilter.value)
    }
    if (search.value.trim()) {
        const q = search.value.trim().toLowerCase()
        list = list.filter((f) => f.name.toLowerCase().includes(q) || f.company.toLowerCase().includes(q))
    }

    const key = sortKey.value
    const dir = sortDir.value === 'asc' ? 1 : -1
    return [...list].sort((a, b) => {
        const av = a[key]
        const bv = b[key]
        if (typeof av === 'string') return av.localeCompare(bv, 'cs') * dir
        return ((av ?? -Infinity) - (bv ?? -Infinity)) * dir
    })
})

// ─── Srovnání (max 3) ───
const MAX_COMPARE = 3
const compareSlugs = ref([])
const compareOpen = ref(false)

const compareFunds = computed(() =>
    compareSlugs.value
        .map((slug) => props.funds.find((f) => f.slug === slug))
        .filter(Boolean),
)

function toggleCompare(slug) {
    const idx = compareSlugs.value.indexOf(slug)
    if (idx >= 0) {
        compareSlugs.value.splice(idx, 1)
    } else if (compareSlugs.value.length < MAX_COMPARE) {
        compareSlugs.value.push(slug)
    }
}

function isCompared(slug) {
    return compareSlugs.value.includes(slug)
}

const compareDisabled = computed(() => compareSlugs.value.length >= MAX_COMPARE)

function onKeydown(e) {
    if (e.key === 'Escape') compareOpen.value = false
}
onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))

// ─── Formátování ───
function fmtPct(v) {
    if (v === null || v === undefined) return '—'
    return v.toLocaleString('cs-CZ', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + ' %'
}
function fmtAssets(mil) {
    if (!mil) return '—'
    return (mil / 1000).toLocaleString('cs-CZ', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' mld. Kč'
}
function returnClass(v) {
    if (v === null || v === undefined) return 'text-slate-400'
    return v >= 0 ? 'text-emerald-600' : 'text-red-600'
}
</script>

<template>
    <div>
        <!-- ─── Filtry ─── -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filtr podle typu fondu">
                <button
                    v-for="filter in FILTERS"
                    :key="filter.value"
                    type="button"
                    class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                    :class="typeFilter === filter.value
                        ? 'bg-slate-900 text-white border-slate-900'
                        : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400'"
                    :aria-pressed="typeFilter === filter.value"
                    @click="typeFilter = filter.value"
                >{{ filter.label }}</button>
            </div>

            <div class="relative sm:ml-auto">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="search"
                    v-model="search"
                    placeholder="Hledat fond…"
                    aria-label="Hledat fond"
                    class="w-full sm:w-56 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                />
            </div>
        </div>

        <!-- ─── Tabulka ─── -->
        <div v-if="visibleFunds.length" class="border border-slate-200 rounded-2xl bg-white shadow-sm overflow-x-auto">
            <table class="w-full min-w-[920px] text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th
                            v-for="col in COLUMNS"
                            :key="col.key"
                            scope="col"
                            class="sticky top-16 bg-slate-50 z-10 text-left font-semibold text-slate-600 px-4 py-3.5 whitespace-nowrap first:sticky first:left-0 first:z-20"
                            :aria-sort="sortKey === col.key ? (sortDir === 'asc' ? 'ascending' : 'descending') : undefined"
                        >
                            <button
                                v-if="col.sortable"
                                type="button"
                                class="inline-flex items-center gap-1 hover:text-slate-900 transition-colors group"
                                @click="toggleSort(col.key)"
                            >
                                {{ col.label }}
                                <span class="text-xs" :class="sortKey === col.key ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'">
                                    {{ sortKey === col.key ? (sortDir === 'asc' ? '▲' : '▼') : '↕' }}
                                </span>
                            </button>
                            <template v-else>{{ col.label }}</template>
                        </th>
                        <th scope="col" class="sticky top-16 bg-slate-50 z-10 text-right font-semibold text-slate-600 px-4 py-3.5">Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="fund in visibleFunds"
                        :key="fund.slug"
                        class="border-b border-slate-100 last:border-0 hover:bg-slate-50/60 transition-colors"
                        :class="isCompared(fund.slug) ? 'bg-emerald-50/40' : ''"
                    >
                        <!-- Název — sticky první sloupec na mobilu -->
                        <td class="sticky left-0 bg-white px-4 py-3.5 min-w-56" :class="isCompared(fund.slug) ? 'bg-emerald-50/40' : ''">
                            <a :href="fund.detailUrl" class="font-semibold text-slate-900 hover:text-emerald-700 transition-colors">
                                {{ fund.name }}
                            </a>
                            <p class="text-xs text-slate-400 mt-0.5">{{ fund.company }}</p>
                            <div v-if="fund.bestReturn || fund.lowestFee" class="flex flex-wrap gap-1.5 mt-1.5">
                                <span v-if="fund.bestReturn" class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wide bg-emerald-600 text-white px-2 py-0.5 rounded-full">
                                    ★ Nejlepší výnos
                                </span>
                                <span v-if="fund.lowestFee" class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wide bg-blue-600 text-white px-2 py-0.5 rounded-full">
                                    Nejnižší poplatek
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full border" :class="TYPE_META[fund.fundType]?.badge">
                                {{ TYPE_META[fund.fundType]?.label ?? fund.fundType }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold tabular-nums" :class="returnClass(fund.return1y)">{{ fmtPct(fund.return1y) }}</td>
                        <td class="px-4 py-3.5 tabular-nums" :class="returnClass(fund.return3y)">{{ fmtPct(fund.return3y) }}</td>
                        <td class="px-4 py-3.5 tabular-nums" :class="returnClass(fund.return5y)">{{ fmtPct(fund.return5y) }}</td>
                        <td class="px-4 py-3.5 text-slate-600 tabular-nums">{{ fmtPct(fund.feeManagement) }}</td>
                        <td class="px-4 py-3.5 text-slate-600 tabular-nums whitespace-nowrap">{{ fmtAssets(fund.assetsMil) }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <a :href="fund.detailUrl" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Detail</a>
                                <button
                                    type="button"
                                    class="text-sm font-medium px-3 py-1.5 rounded-lg border transition-all"
                                    :class="isCompared(fund.slug)
                                        ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                        : compareDisabled
                                            ? 'border-slate-100 text-slate-300 cursor-not-allowed'
                                            : 'border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-700'"
                                    :disabled="!isCompared(fund.slug) && compareDisabled"
                                    @click="toggleCompare(fund.slug)"
                                >{{ isCompared(fund.slug) ? '✓ Vybráno' : 'Srovnat' }}</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ─── Empty state ─── -->
        <div v-else class="border border-slate-200 rounded-2xl bg-white py-16 text-center">
            <svg class="h-10 w-10 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-slate-600 font-medium mb-1">Žádný fond neodpovídá zadaným filtrům</p>
            <p class="text-sm text-slate-400 mb-5">Zkuste změnit typ fondu nebo upravit hledaný výraz.</p>
            <button
                type="button"
                class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors"
                @click="typeFilter = 'all'; search = ''"
            >Zrušit filtry</button>
        </div>

        <!-- ─── Plovoucí lišta srovnání ─── -->
        <Transition
            enter-active-class="transition-all duration-200" enter-from-class="opacity-0 translate-y-4"
            leave-active-class="transition-all duration-150" leave-to-class="opacity-0 translate-y-4"
        >
            <div v-if="compareSlugs.length" class="fixed bottom-5 inset-x-0 z-40 px-4 print:hidden">
                <div class="max-w-xl mx-auto bg-slate-900 text-white rounded-2xl shadow-xl px-5 py-3.5 flex items-center justify-between gap-4">
                    <p class="text-sm">
                        <span class="font-bold">{{ compareSlugs.length }}/{{ MAX_COMPARE }}</span>
                        <span class="text-slate-300"> {{ compareSlugs.length === 1 ? 'fond vybrán' : compareSlugs.length <= 4 ? 'fondy vybrány' : 'fondů vybráno' }}</span>
                    </p>
                    <div class="flex items-center gap-3">
                        <button type="button" class="text-sm text-slate-400 hover:text-white transition-colors" @click="compareSlugs = []">Zrušit</button>
                        <button
                            type="button"
                            class="bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors disabled:opacity-40"
                            :disabled="compareSlugs.length < 2"
                            @click="compareOpen = true"
                        >Porovnat</button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ─── Modal srovnání ─── -->
        <Teleport to="body">
            <div
                v-if="compareOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog" aria-modal="true" aria-label="Srovnání fondů"
            >
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="compareOpen = false"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                        <h2 class="text-lg font-bold text-slate-900">Srovnání fondů</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1" aria-label="Zavřít" @click="compareOpen = false">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 overflow-x-auto">
                        <table class="w-full text-sm min-w-[560px]">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-left font-medium text-slate-400 text-xs uppercase tracking-wide pb-4 pr-4 w-40"></th>
                                    <th v-for="fund in compareFunds" :key="fund.slug" scope="col" class="text-left pb-4 px-3 align-top">
                                        <a :href="fund.detailUrl" class="font-bold text-slate-900 hover:text-emerald-700 leading-snug block">{{ fund.name }}</a>
                                        <span class="text-xs font-normal text-slate-400">{{ fund.company }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Typ fondu</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3">
                                        <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full border" :class="TYPE_META[fund.fundType]?.badge">
                                            {{ TYPE_META[fund.fundType]?.label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Výnos 1 rok</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 font-semibold tabular-nums" :class="returnClass(fund.return1y)">{{ fmtPct(fund.return1y) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Výnos 3 roky (p.a.)</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 tabular-nums" :class="returnClass(fund.return3y)">{{ fmtPct(fund.return3y) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Výnos 5 let (p.a.)</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 tabular-nums" :class="returnClass(fund.return5y)">{{ fmtPct(fund.return5y) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Poplatek za správu</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 text-slate-700 tabular-nums">{{ fmtPct(fund.feeManagement) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Poplatek z výnosu</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 text-slate-700 tabular-nums">{{ fmtPct(fund.feePerformance) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Majetek</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 text-slate-700 tabular-nums">{{ fmtAssets(fund.assetsMil) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left font-medium text-slate-500 py-3 pr-4">Počet účastníků</th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3 text-slate-700 tabular-nums">{{ fund.participants ? fund.participants.toLocaleString('cs-CZ') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="py-3 pr-4"></th>
                                    <td v-for="fund in compareFunds" :key="fund.slug" class="py-3 px-3">
                                        <a :href="fund.detailUrl" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                            Detail fondu
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-400">
                            Upozornění: Tento web může získat provizi za sjednání produktu prostřednictvím
                            uvedených odkazů. Tato skutečnost neovlivňuje naše hodnocení ani pořadí fondů.
                        </p>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
