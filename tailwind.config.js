import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './Modules/**/resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    header: '#1E3A8A',
                    accent: '#3B82F6',
                    bg: '#F3F4F6',
                    positive: '#10B981',
                    danger: '#EF4444',
                },
            },
            fontFamily: {
                sans: ['Vazirmatn', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
