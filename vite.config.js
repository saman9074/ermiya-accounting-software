import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import { fileURLToPath } from 'url';

// راه‌حل استاندارد برای به دست آوردن __dirname در ماژول‌های ESM
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'Modules/**/resources/js/app.js',
            ],
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
            '@': path.resolve(__dirname, 'resources/js'),
            '@Core': path.resolve(__dirname, 'Modules/Core/resources/js'),
            '@Persons': path.resolve(__dirname, 'Modules/Persons/resources/js'),
            '@Inventory': path.resolve(__dirname, 'Modules/Inventory/resources/js'),
        },
    },
});

