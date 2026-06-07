import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // KUNCI UTAMA: Tambahkan baris ini agar mode bisa di-switch via class
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Kamu bisa tambahkan warna Al-Iman kamu di sini agar gampang dipanggil
                'al-iman-green': '#0F6E56',
                'al-iman-dark': '#121212',
            },
        },
    },

    plugins: [forms],
};