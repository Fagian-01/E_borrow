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

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                // Deep Sapphire Blue Palette
                sapphire: {
                    50: '#eef3ff',
                    100: '#dce6ff',
                    200: '#b2cbff',
                    300: '#7da8ff',
                    400: '#4a7fff',
                    500: '#1e56e0',
                    600: '#1444c4',
                    700: '#0f3399',
                    800: '#0c2a7a',
                    900: '#0a1f5c',
                    950: '#060e2b',
                },
                // Champagne Gold Palette
                gold: {
                    50: '#fef9ee',
                    100: '#fdf0d3',
                    200: '#fbe0a5',
                    300: '#f8cb6d',
                    400: '#f5b642',
                    500: '#d4af37',
                    600: '#c49a2a',
                    700: '#a07823',
                    800: '#835f22',
                    900: '#6c4f20',
                    950: '#3d2a0f',
                },
                // Space / Cosmic Dark Theme
                cosmic: {
                    50: '#f0f1f8',
                    100: '#d9ddef',
                    200: '#b5bcdf',
                    300: '#8a95cb',
                    400: '#636fb3',
                    500: '#4a5599',
                    600: '#3b437d',
                    700: '#2d3363',
                    800: '#1a1f3d',
                    900: '#0d1025',
                    950: '#070816',
                },
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                'glow-gold': '0 0 20px rgba(212, 175, 55, 0.15)',
                'glow-sapphire': '0 0 20px rgba(30, 86, 224, 0.15)',
                'card': '0 4px 24px rgba(0, 0, 0, 0.06)',
                'card-hover': '0 12px 40px rgba(0, 0, 0, 0.12)',
                'card-dark': '0 4px 24px rgba(0, 0, 0, 0.3)',
                'card-dark-hover': '0 12px 40px rgba(0, 0, 0, 0.5)',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'space-gradient': 'linear-gradient(135deg, #060e2b 0%, #0d1025 30%, #1a1f3d 70%, #0a1f5c 100%)',
                'gold-gradient': 'linear-gradient(135deg, #d4af37 0%, #f5b642 50%, #f8cb6d 100%)',
                'sapphire-gradient': 'linear-gradient(135deg, #0c2a7a 0%, #1e56e0 50%, #4a7fff 100%)',
            },
            animation: {
                'float': 'float 6s ease-in-out infinite',
                'twinkle': 'twinkle 3s ease-in-out infinite',
                'slide-up': 'slideUp 0.5s ease-out',
                'fade-in': 'fadeIn 0.4s ease-out',
                'pulse-gold': 'pulseGold 2s ease-in-out infinite',
                'shooting-star': 'shootingStar 3s ease-in-out infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                twinkle: {
                    '0%, 100%': { opacity: '0.3' },
                    '50%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                pulseGold: {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgba(212, 175, 55, 0.3)' },
                    '50%': { boxShadow: '0 0 20px 4px rgba(212, 175, 55, 0.15)' },
                },
                shootingStar: {
                    '0%': { transform: 'translateX(-100%) translateY(-100%)', opacity: '1' },
                    '70%': { opacity: '1' },
                    '100%': { transform: 'translateX(200%) translateY(200%)', opacity: '0' },
                },
            },
            borderRadius: {
                '4xl': '2rem',
            },
            transitionDuration: {
                '400': '400ms',
            },
        },
    },

    plugins: [forms],
};
