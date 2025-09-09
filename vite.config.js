import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { glob } from 'glob'; // <-- ابزار glob را وارد می‌کنیم

// یک آرایه از تمام فایل‌های app.js در ماژول‌ها و پوشه اصلی پیدا می‌کنیم
const moduleInputs = glob.sync('Modules/*/resources/js/app.js');
const mainInput = 'resources/js/app.js';

export default defineConfig({
    plugins: [
        laravel({
            // ورودی را به صورت دینامیک با تمام مسیرهای پیدا شده تنظیم می‌کنیم
            input: [mainInput, ...moduleInputs],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            // این بخش بدون تغییر باقی می‌ماند
            '@/Modules/Core': '/Modules/Core/resources/js',
            '@/Modules/Persons': '/Modules/Persons/resources/js',
        },
    },
});

