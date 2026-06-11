# Důchody.cz — Design systém

> Tento dokument je source of truth pro všechna vizuální rozhodnutí. Každý komponent musí odpovídat těmto specifikacím. UI stavíme z **Blade komponent** (statika) a **Vue 3 islands** (interaktivita).

---

## Filosofie

**Anti-AI-slop.** Web musí vypadat jako produkt, ne jako vygenerovaný obsah. Referenční weby:
- [Linear.app](https://linear.app) — čistota, typografie, whitespace
- [Stripe](https://stripe.com) — důvěryhodnost, datové karty
- [Wise](https://wise.com) — finanční, přehledný, moderní
- ČSSZ estetiku vědomě odmítáme

**Principy:**
1. **Data first** — čísla jsou hvězda, ne dekorace
2. **Čitelnost pro 50+** — minimální font-size 16px v obsahu, vysoké kontrasty
3. **Mobilní realita** — 60 %+ traffic z mobilu
4. **Rychlost** — server-side Blade, JS jen kde přidává hodnotu

---

## Barvy

### Tailwind konfigurace (`tailwind.config.js`)

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        // Brand — Slate jako primární neutrál
        slate: {
          50:  '#f8fafc',
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#94a3b8',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1e293b',
          900: '#0f172a',
          950: '#020617',
        },
        // Akcent — Emerald
        emerald: {
          50:  '#ecfdf5',
          100: '#d1fae5',
          200: '#a7f3d0',
          300: '#6ee7b7',
          400: '#34d399',
          500: '#10b981',
          600: '#059669',  // ← Primary CTA
          700: '#047857',  // ← CTA hover
          800: '#065f46',
          900: '#064e3b',
        },
      },
      fontFamily: {
        sans: ['Inter Variable', 'Inter', 'system-ui', 'sans-serif'],
        serif: ['Instrument Serif', 'Georgia', 'serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      fontSize: {
        'xs':   ['12px', { lineHeight: '1.5' }],
        'sm':   ['14px', { lineHeight: '1.5' }],
        'base': ['16px', { lineHeight: '1.7' }],
        'lg':   ['18px', { lineHeight: '1.6' }],
        'xl':   ['20px', { lineHeight: '1.5' }],
        '2xl':  ['24px', { lineHeight: '1.3' }],
        '3xl':  ['30px', { lineHeight: '1.25', letterSpacing: '-0.01em' }],
        '4xl':  ['36px', { lineHeight: '1.2',  letterSpacing: '-0.02em' }],
        '5xl':  ['48px', { lineHeight: '1.1',  letterSpacing: '-0.03em' }],
        '6xl':  ['60px', { lineHeight: '1.05', letterSpacing: '-0.04em' }],
      },
      borderRadius: {
        'sm': '4px',
        DEFAULT: '8px',
        'md': '8px',
        'lg': '12px',
        'xl': '16px',
        '2xl': '20px',
        '3xl': '24px',
      },
      boxShadow: {
        'sm': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
        DEFAULT: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
        'md': '0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.07)',
        'lg': '0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07)',
        'none': 'none',
      },
      animation: {
        'count-up': 'countUp 1s ease-out forwards',
        'fade-in':  'fadeIn 0.3s ease-out',
        'slide-up': 'slideUp 0.4s ease-out',
      },
      keyframes: {
        countUp: {
          from: { opacity: '0', transform: 'translateY(8px)' },
          to:   { opacity: '1', transform: 'translateY(0)' },
        },
        fadeIn: {
          from: { opacity: '0' },
          to:   { opacity: '1' },
        },
        slideUp: {
          from: { opacity: '0', transform: 'translateY(16px)' },
          to:   { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
}
```

### Sémantické použití barev

```
Pozadí stránky:     slate-50   (#f8fafc)
Card pozadí:        white      (#ffffff)
Card border:        slate-200  (#e2e8f0)
Heading text:       slate-900  (#0f172a)
Body text:          slate-700  (#334155)
Muted text:         slate-500  (#64748b)
Disabled text:      slate-400  (#94a3b8)
Placeholder:        slate-300  (#cbd5e1)

Primární akce:      emerald-600 (#059669)
Primární hover:     emerald-700 (#047857)
Akční surface:      emerald-50  (#ecfdf5)
Akční border:       emerald-200 (#a7f3d0)

Upozornění:         amber-500   (#f59e0b)
Chyba/krácení:      red-500     (#ef4444)
Info:               blue-500    (#3b82f6)
Úspěch/growth:      emerald-500 (#10b981)
```

---

## Typografie

### Fonty (self-hosted přes @font-face)

Fonty hostujeme lokálně v `public/fonts/` a registrujeme v CSS (žádný `next/font`). Inter Variable a Instrument Serif stáhnout jako `.woff2`.

```css
/* resources/css/app.css */
@font-face {
  font-family: 'Inter Variable';
  src: url('/fonts/InterVariable.woff2') format('woff2');
  font-weight: 100 900;
  font-display: swap;
  font-style: normal;
}

@font-face {
  font-family: 'Instrument Serif';
  src: url('/fonts/InstrumentSerif-Regular.woff2') format('woff2');
  font-weight: 400;
  font-display: swap;
  font-style: normal;
}

@font-face {
  font-family: 'Instrument Serif';
  src: url('/fonts/InstrumentSerif-Italic.woff2') format('woff2');
  font-weight: 400;
  font-display: swap;
  font-style: italic;
}

/* Preload v <head> layoutu pro rychlý LCP:
   <link rel="preload" href="/fonts/InterVariable.woff2" as="font" type="font/woff2" crossorigin> */
```

### Typografická hierarchie

```css
/* H1 — hero headlines */
.h1 {
  font-size: clamp(36px, 5vw, 60px);
  font-weight: 700;
  letter-spacing: -0.04em;
  line-height: 1.05;
  color: #0f172a;
}

/* H2 — sekce headlines */
.h2 {
  font-size: clamp(24px, 3vw, 36px);
  font-weight: 700;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

/* H3 — karty, podnadpisy */
.h3 {
  font-size: clamp(18px, 2vw, 24px);
  font-weight: 600;
  letter-spacing: -0.01em;
}

/* Perex / lead text */
.lead {
  font-size: clamp(16px, 1.5vw, 20px);
  line-height: 1.6;
  color: #475569;
  font-weight: 400;
}

/* Číselné hodnoty (důchodové výsledky) */
.number-display {
  font-size: clamp(32px, 4vw, 48px);
  font-weight: 700;
  letter-spacing: -0.03em;
  font-variant-numeric: tabular-nums;
  color: #0f172a;
}

/* Editorální perex — serif */
.editorial-lead {
  font-family: 'Instrument Serif', Georgia, serif;
  font-size: 20px;
  line-height: 1.6;
  font-style: italic;
  color: #334155;
}
```

---

## Komponenty

> Statické prvky stavíme jako **Blade komponenty** v `resources/views/components/`, volané přes `<x-card>`, `<x-button>` atd. Interaktivní prvky jsou **Vue komponenty** v `resources/js/components/`.

### Karty (Blade)

```blade
{{-- resources/views/components/data-card.blade.php --}}
@props(['label', 'value', 'trend' => null])

<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow']) }}>
  <p class="text-sm text-slate-500 mb-1">{{ $label }}</p>
  <p class="text-3xl font-bold text-slate-900 tabular-nums">{{ $value }}</p>
  @if($trend)
    <p class="text-sm text-emerald-600 mt-1">↑ {{ $trend }}</p>
  @endif
</div>

{{-- Použití --}}
<x-data-card label="Průměrný starobní důchod" value="21 400 Kč" trend="+358 Kč od ledna 2026" />
```

```blade
{{-- Akční karta (CTA) --}}
<div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6">
  <p class="text-emerald-800 font-semibold">Spočítejte si svůj důchod</p>
  <x-button class="mt-3" href="/kalkulacka/vyse">Kalkulačka →</x-button>
</div>

{{-- Informační box v článku --}}
<div class="border-l-4 border-emerald-500 bg-emerald-50 rounded-r-lg p-4 my-6">
  <p class="font-semibold text-emerald-800">Tip</p>
  <p class="text-emerald-700 mt-1 text-sm">...</p>
</div>
```

### Tlačítka (Blade)

```blade
{{-- resources/views/components/button.blade.php --}}
@props(['variant' => 'primary', 'href' => null])

@php
  $base = 'inline-flex items-center font-semibold rounded-lg transition-colors';
  $styles = match($variant) {
    'primary'   => 'bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3',
    'secondary' => 'border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-3',
    'ghost'     => 'text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 px-4 py-2',
    'hero'      => 'bg-emerald-600 hover:bg-emerald-700 text-white text-lg px-8 py-4 rounded-xl shadow-sm hover:shadow-md',
  };
  $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => "$base $styles"]) }}>
  {{ $slot }}
</{{ $tag }}>

{{-- Použití --}}
<x-button>Spočítat důchod</x-button>
<x-button variant="secondary">Více informací</x-button>
<x-button variant="ghost">Zobrazit vše →</x-button>
<x-button variant="hero">
  Spočítat důchod
  <svg class="ml-2 h-5 w-5"><!-- ArrowRight ikona --></svg>
</x-button>
```

### Formuláře (kalkulačky — Vue island)

Interaktivní formuláře jsou Vue komponenty s reaktivním stavem. Příklad pole:

```vue
<!-- resources/js/components/calculators/fields/DateField.vue -->
<script setup>
defineProps({ label: String, modelValue: String, hint: String, required: Boolean })
defineEmits(['update:modelValue'])
</script>

<template>
  <div class="space-y-2">
    <label class="text-sm font-medium text-slate-700">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    <input
      type="date"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500"
    />
    <p v-if="hint" class="text-xs text-slate-500">{{ hint }}</p>
  </div>
</template>
```

Select vzor (Vue):

```vue
<select
  v-model="gender"
  class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-emerald-500"
>
  <option value="" disabled>Vyberte pohlaví</option>
  <option value="M">Muž</option>
  <option value="F">Žena</option>
</select>
```

### Badges & tagy (Blade)

```blade
{{-- Kategorie --}}
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
  Aktuálně
</span>

{{-- Upozornění --}}
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
  <svg class="h-3 w-3"><!-- AlertCircle --></svg>
  Novinka
</span>

{{-- Neutrální info --}}
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
  2026
</span>
```

### Skeleton loading (Vue)

```vue
<!-- Zobrazit ve Vue komponentě dokud běží výpočet/fetch -->
<div v-if="loading" class="bg-white border border-slate-200 rounded-xl p-6 animate-pulse">
  <div class="h-4 bg-slate-200 rounded w-32 mb-3"></div>
  <div class="h-10 bg-slate-200 rounded w-28"></div>
  <div class="h-3 bg-slate-100 rounded w-20 mt-2"></div>
</div>
```

---

## Layout patterns

### Hero sekce (Blade)

```blade
{{-- Fullwidth hero s background pattern --}}
<section class="relative bg-slate-50 overflow-hidden">
  {{-- Subtle grid pattern --}}
  <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:48px_48px] opacity-40"></div>

  {{-- Emerald glow — subtilní, ne přehnané --}}
  <div class="absolute top-0 right-1/4 w-96 h-96 bg-emerald-200/20 rounded-full blur-3xl"></div>

  <div class="relative max-w-7xl mx-auto px-4 py-24 sm:py-32">
    {{-- Obsah --}}
  </div>
</section>
```

### Stats sekce (Vue island — animovaná čísla)

V Blade rezervujeme místo a připojíme Vue komponentu:

```blade
{{-- Blade --}}
<div id="stats-section" data-stats='{"avgPension":21400,"valorization":358,"pensioners":3100000}'></div>
```

```js
// resources/js/app.js — mount
import { createApp } from 'vue'
import StatsSection from './components/StatsSection.vue'

const el = document.getElementById('stats-section')
if (el) {
  createApp(StatsSection, { ...JSON.parse(el.dataset.stats) }).mount(el)
}
```

### Navigace (Blade)

```blade
{{-- resources/views/components/layout/header.blade.php --}}
{{-- Sticky nav s blur efektem (funkční, ne dekorativní) --}}
<header class="sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
  <nav class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      <span class="text-xl font-bold text-slate-900">důchody</span>
      <span class="text-xl font-bold text-emerald-600">.cz</span>
    </a>
    {{-- Links / CTA --}}
  </nav>
</header>
```

---

## Animace (@vueuse/motion + CSS)

### Principy
- Animace jen **jednou** při prvním zobrazení (`v-motion` s intersection observer / `whileInView`)
- Délka: 0.3–0.6s pro UI, max 1s pro hero
- Easing: `ease-out` nebo vlastní spring
- Respektovat `prefers-reduced-motion` (kontrolovat v komponentě a vypnout)

### Vzory

```vue
<!-- Stagger children (seznam karet) — @vueuse/motion -->
<script setup>
// npm i @vueuse/motion ; registrovat MotionPlugin v app.js
</script>

<template>
  <ul>
    <li
      v-for="(item, i) in items"
      :key="item.id"
      v-motion
      :initial="{ opacity: 0, y: 20 }"
      :visible-once="{ opacity: 1, y: 0, transition: { duration: 400, delay: i * 100 } }"
    >
      {{ item.label }}
    </li>
  </ul>
</template>
```

```vue
<!-- Animated counter (Vue + requestAnimationFrame) -->
<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({ value: Number, suffix: { type: String, default: '' } })
const display = ref('0')

onMounted(() => {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    display.value = props.value.toLocaleString('cs-CZ') + props.suffix
    return
  }
  const duration = 1500
  const start = performance.now()
  const tick = (now) => {
    const t = Math.min((now - start) / duration, 1)
    const eased = 1 - Math.pow(1 - t, 3) // ease-out cubic
    display.value = Math.round(props.value * eased).toLocaleString('cs-CZ') + props.suffix
    if (t < 1) requestAnimationFrame(tick)
  }
  requestAnimationFrame(tick)
})
</script>

<template>
  <span>{{ display }}</span>
</template>
```

---

## Accessibility checklist

Každá stránka musí splňovat:

- [ ] Landmark regions (`<main>`, `<nav>`, `<header>`, `<footer>`)
- [ ] Skip navigation link jako první focusovatelný element
- [ ] Smysluplné `<title>` (max 60 znaků)
- [ ] `lang="cs"` na `<html>`
- [ ] Všechny obrázky mají `alt` atribut
- [ ] Kontrastní poměr: min 4.5:1 pro text, 3:1 pro UI prvky
- [ ] Kalkulačkové formuláře: `aria-describedby` pro nápovědu
- [ ] Výsledky kalkulačky: `role="status"` nebo `aria-live="polite"` (Vue komponenta)
- [ ] Focus visible na všech interaktivních prvcích
- [ ] Tlačítka mají smysluplné texty (ne "Klikni zde")

---

## Responsive breakpointy

```
xs:  320px  — malé telefony
sm:  640px  — telefony (landscape)
md:  768px  — tablety
lg:  1024px — malé laptopy
xl:  1280px — desktopy (max-width container)
2xl: 1536px — velké obrazovky
```

### Kalkulačka na mobilu
Celá kalkulačka musí být fullscreen-friendly na 375px:
- Formulářové prvky min 44px výška (touch targets)
- Výsledky sticky na dně (fixed bottom bar)
- Klávesnice nesmí zakrývat pole s výsledky

---

## Dark mode

Dark mode je implementován přes CSS variables (ne Tailwind `dark:` classes) pro výkon:

```css
/* resources/css/app.css */
:root {
  --bg-primary: #f8fafc;
  --bg-card: #ffffff;
  --border-color: #e2e8f0;
  --text-primary: #0f172a;
  --text-muted: #475569;
  --accent: #059669;
}

@media (prefers-color-scheme: dark) {
  :root {
    --bg-primary: #0a0f1a;
    --bg-card: #111827;
    --border-color: #1f2937;
    --text-primary: #f1f5f9;
    --text-muted: #94a3b8;
    --accent: #10b981;
  }
}
```

**Poznámka:** Dark mode jako sekundární priorita. Cílová skupina 50+ preferuje light mode. Implementujeme, ale neoptimalizujeme jako první.
