import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Nunito Sans"', ...defaultTheme.fontFamily.sans],
                script: ['"Hidayatullah"', 'cursive'],
            },
            colors: {
                brand: {
                    50: '#fbf3f3',
                    100: '#f6e4e4',
                    200: '#ecc8c8',
                    300: '#dda2a1',
                    400: '#c56c6b',
                    500: '#6a1918',
                    600: '#5c1615',
                    700: '#4c1211',
                    800: '#3e0f0e',
                    900: '#2f0c0b',
                },
                sun: {
                    400: '#fab617',
                    500: '#e6a512',
                },
                cocoa: '#6b3f01',
                night: {
                    DEFAULT: '#0c0c0c',
                    soft: '#1f1f1f',
                },
            },
        },
    },

    plugins: [forms],
};
