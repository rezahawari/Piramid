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
                    50: '#f6fdf9',
                    100: '#e3f6ee',
                    200: '#9ddbbd',
                    300: '#6fc4a0',
                    400: '#53b691',
                    500: '#43a984',
                    600: '#318a6b',
                    700: '#2a7259',
                    800: '#235c49',
                    900: '#1d4a3b',
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
