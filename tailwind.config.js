import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./app/**/*.php",
        // "./app/Extensions/**/*.php",
    ],

    safelist: ['inline', 'text-red-600', 'mr-2', 'font-bold', 'no-underline'],

    theme: {
        container: {
            center: true,
        },
    },

    plugins: [
        require('@tailwindcss/typography'),
    ],
};
