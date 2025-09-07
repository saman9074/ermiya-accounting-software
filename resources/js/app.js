import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        // بررسی کنید آیا نام صفحه شامل نام یک ماژول است
        const parts = name.split('::');
        if (parts.length > 1) {
            const module = parts[0];
            const page = parts[1];
            // صفحه را از داخل پوشه ماژول مربوطه بارگذاری کنید
            return resolvePageComponent(`/Modules/${module}/resources/js/Pages/${page}.vue`, import.meta.glob('/Modules/*/resources/js/Pages/**/*.vue'));
        }
        // در غیر این صورت، از پوشه پیش‌فرض لاراول بارگذاری کنید
        return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
