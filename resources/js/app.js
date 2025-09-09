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
        let pagePath;

        // 1) پشتیبانی از فرمت Module::Page
        if (name.includes('::')) {
            const [module, page] = name.split('::');
            pagePath = Object.keys(pages).find(
                path => path.endsWith(`/Modules/${module}/resources/js/Pages/${page}.vue`)
            );
        } else {
            // 2) پشتیبانی از صفحات اصلی
            pagePath = `./Pages/${name}.vue`;
        }

        const page = pages[pagePath];

        if (!page) {
            throw new Error(`Page not found: ${name}`);
        }

        // تابع را اجرا کن تا کامپوننت بارگذاری شود
        return page();
    },

    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },

    progress: { color: '#4B5563' },
});
