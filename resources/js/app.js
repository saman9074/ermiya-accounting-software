import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// همه صفحات رو (اصلی + ماژول‌ها) لود کن
const pages = import.meta.glob([
    './Pages/**/*.vue',
    '../../Modules/**/resources/js/Pages/**/*.vue'
]);

createInertiaApp({
    title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'Laravel'}`,

    resolve: (name) => {
        // 1) پشتیبانی از Persons::Index
        if (name.includes('::')) {
            const [module, page] = name.split('::'); // مثلا Persons::Index
            const modulePage = Object.keys(pages).find(
                path => path.endsWith(`/Modules/${module}/resources/js/Pages/${page}.vue`)
            );

            if (modulePage) {
                return pages[modulePage]();
            }
        }

        // 2) صفحات اصلی
        if (pages[`./Pages/${name}.vue`]) {
            return pages[`./Pages/${name}.vue`];
        }

        throw new Error(`Page not found: ${name}`);
    },

    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },

    progress: { color: '#4B5563' },
});
