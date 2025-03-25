/** @type {import('tailwindcss').Config} */
const { fontFamily } = require('tailwindcss/defaultTheme')

export default {
    darkMode: 'selector',

    content: [
        './resources/**/*.{js,blade.php}',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './app/**/*.php',
    ],

    safelist: ['inline', 'text-red-600', 'mr-2', 'font-bold', 'no-underline'],

    theme: {
        extend: {
            colors: {
                mirage: '#141624',
                haiti: '#16182C',
                cloud: '#2b2e53',
            },
            fontFamily: {
                poppins: "'Poppins', Verdana, sans-serif",
            },
        },
        container: {
            center: true,
        },
    },

    plugins: [require('@tailwindcss/typography')],
}
