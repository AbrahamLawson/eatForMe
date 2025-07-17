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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                title: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'raspberry': '#EF476F',  // Rouge framboise
                'soft-orange': '#FFA552', // Orange doux
                'mint': '#3ECF8E',       // Vert menthe clair
                'off-white': '#FDFDFD',  // Blanc cassé
                'charcoal': '#333333',   // Gris charbon doux
                'light-gray': '#E8E8E8', // Gris clair
            },
        },
    },

    plugins: [forms],
};
