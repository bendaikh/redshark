import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
                primary: {
                    50: '#FFF5EB',
                    100: '#FFE8D1',
                    200: '#FFD0A3',
                    300: '#FFB366',
                    400: '#FF9633',
                    500: '#F58220',
                    600: '#E06D0A',
                    700: '#B85408',
                    800: '#8F4209',
                    900: '#6B3208',
                    950: '#3D1C04',
                },
                sidebar: {
                    DEFAULT: '#1A1C2C',
                    light: '#252838',
                },
                surface: {
                    warm: '#FFF9F5',
                },
                // Map indigo to primary orange so existing UI picks up the brand color
                indigo: {
                    50: '#FFF5EB',
                    100: '#FFE8D1',
                    200: '#FFD0A3',
                    300: '#FFB366',
                    400: '#FF9633',
                    500: '#F58220',
                    600: '#E06D0A',
                    700: '#B85408',
                    800: '#8F4209',
                    900: '#6B3208',
                    950: '#3D1C04',
                },
            },
        },
    },

    plugins: [forms],
};
