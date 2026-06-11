import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { MotionPlugin } from '@vueuse/motion';

import HeroStats from './components/HeroStats.vue';
import QuickCalculator from './components/QuickCalculator.vue';
import StatsCounter from './components/StatsCounter.vue';
import PensionCalculator from './components/calculators/PensionCalculator.vue';
import FundsTable from './components/funds/FundsTable.vue';
import FundChart from './components/funds/FundChart.vue';
import SavingsCalculator from './components/funds/SavingsCalculator.vue';
import DipTaxCalculator from './components/funds/DipTaxCalculator.vue';
import StatsDashboard from './components/stats/StatsDashboard.vue';
import RegionTrendChart from './components/stats/RegionTrendChart.vue';

// ─── Vue island mount helper ───
function mountIsland(id, component, propsResolver = (el) => JSON.parse(el.dataset.props || '{}')) {
    const el = document.getElementById(id) ?? document.querySelector(id);
    if (!el) return;

    const app = createApp(component, propsResolver(el));
    app.use(MotionPlugin);
    app.mount(el);
}

// ─── Homepage islands ───
mountIsland('hero-stats', HeroStats);
mountIsland('quick-calculator', QuickCalculator);
mountIsland('stats-counter', StatsCounter);

// ─── Kalkulačka výše důchodu ───
mountIsland('pension-calculator', PensionCalculator);

// ─── Penzijní fondy ───
mountIsland('funds-table', FundsTable);
mountIsland('fund-chart', FundChart);
mountIsland('savings-calculator', SavingsCalculator);
mountIsland('dip-tax-calculator', DipTaxCalculator);

// ─── Statistiky ───
mountIsland('stats-dashboard', StatsDashboard);
mountIsland('region-trend-chart', RegionTrendChart);

// ─── Sdílení článků (Web Share API + clipboard fallback) ───
document.addEventListener('click', async (e) => {
    const button = e.target.closest('[data-share]');
    if (!button) return;

    const title = button.dataset.shareTitle || document.title;
    const url = location.href;

    if (navigator.share) {
        try {
            await navigator.share({ title, url });
        } catch {
            // uživatel zrušil dialog — žádná akce
        }
        return;
    }

    try {
        await navigator.clipboard.writeText(url);
        const label = button.querySelector('[data-share-label]');
        if (label) {
            const original = label.textContent;
            label.textContent = 'Odkaz zkopírován ✓';
            setTimeout(() => (label.textContent = original), 2500);
        }
    } catch {
        prompt('Zkopírujte odkaz:', url);
    }
});

// ─── Mobile menu (pure JS) ───
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.getAttribute('aria-hidden') === 'false';
            mobileMenu.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
            mobileMenu.classList.toggle('hidden', isOpen);
            menuToggle.setAttribute('aria-expanded', String(!isOpen));
        });
    }
});
