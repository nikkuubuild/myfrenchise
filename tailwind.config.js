import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: {
                    primary: '#172B3A',
                    secondary: '#334E68',
                    muted: '#586B7B',
                },
                surface: {
                    canvas: '#FFFFFF',
                    paper: '#F6F3ED',
                    card: '#FFFFFF',
                    subtle: '#EFECE6',
                },
                border: {
                    subtle: '#E5E0D8',
                    strong: '#C8C0B4',
                },
                accent: {
                    green: '#49685B',
                    'green-bg': '#EDF3F0',
                    brass: '#D7B77A',
                    'navy-light': '#243F53',
                },
                status: {
                    verified: {
                        text: '#1B4D3E',
                        bg: '#EAF4F0',
                        border: '#A3D2BE',
                    },
                    pending: {
                        text: '#7A5200',
                        bg: '#FFF8EB',
                        border: '#E5C578',
                    },
                    caveat: {
                        text: '#8A4500',
                        bg: '#FDF4ED',
                        border: '#F4B98E',
                    },
                    rejected: {
                        text: '#8B1E1E',
                        bg: '#FDF1F1',
                        border: '#F1AAAA',
                    },
                },
            },
            fontFamily: {
                serif: ['"Source Serif 4"', 'Playfair Display', 'Georgia', 'serif'],
                sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
            },
        },
    },
    plugins: [],
};
