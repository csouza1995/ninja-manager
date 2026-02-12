import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        require('daisyui'),
    ],
    daisyui: {
        themes: [
            {
                ninja: {
                    "primary": "#22c55e",           // green-500
                    "secondary": "#3f3f46",         // zinc-700
                    "accent": "#22c55e",            // green-500
                    "neutral": "#18181b",           // zinc-900
                    "base-100": "#000000",          // black
                    "base-200": "#09090b",          // zinc-950
                    "base-300": "#18181b",          // zinc-900
                    "info": "#3b82f6",              // blue-500
                    "success": "#22c55e",           // green-500
                    "warning": "#eab308",           // yellow-500
                    "error": "#ef4444",             // red-500
                },
            },
        ],
        darkTheme: "ninja",
        base: true,
        styled: true,
        utils: true,
    },
};
