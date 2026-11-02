import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50:  '#eefbf3',
                    100: '#d6f5e1',
                    200: '#b0eac6',
                    300: '#7ddba4',
                    400: '#48c67e',
                    500: '#33b868', // ← фирменный цвет Autolearn
                    600: '#229553',
                    700: '#1c7744',
                    800: '#195e38',
                    900: '#154d2f',
                    950: '#072b18',
                },
            },
        },
    },

    plugins: [forms],
};