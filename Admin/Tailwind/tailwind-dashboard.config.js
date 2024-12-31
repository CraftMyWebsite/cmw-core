/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: [
        './Admin/**/*.{php,html,js}',
        './Admin/Tailwind/**/*.{php,html,js}',
        './App/Package/**/Views/**/*.{php,html,js}',
        './node_modules/flowbite/**/*.js'
    ],
    theme: {
        extend: {
            colors: {
                'light-primary': 'var(--light-primary)',
                'light-secondary': 'var(--light-secondary)',
                'light-third': 'var(--light-third)',
                'light-fourth': 'var(--light-fourth)',
                'light-text-primary': 'var(--light-text-primary)',
                'light-text-secondary': 'var(--light-text-secondary)',
                'light-input-bg': 'var(--light-input-bg)',
                'light-scrollbar': 'var(--light-scrollbar)',
                'light-scrollbar-hover': 'var(--light-scrollbar-hover)',
                'light-scrollbar-bg': 'var(--light-scrollbar-bg)',

                'dark-primary': 'var(--dark-primary)',
                'dark-secondary': 'var(--dark-secondary)',
                'dark-third': 'var(--dark-third)',
                'dark-fourth': 'var(--dark-fourth)',
                'dark-text-primary': 'var(--dark-text-primary)',
                'dark-text-secondary': 'var(--dark-text-secondary)',
                'dark-input-bg': 'var(--dark-input-bg)',
                'dark-scrollbar': 'var(--dark-scrollbar)',
                'dark-scrollbar-hover': 'var(--dark-scrollbar-hover)',
                'dark-scrollbar-bg': 'var(--dark-scrollbar-bg)',

                'nav-sky': 'var(--nav-sky)',
                'nav-sky-light': 'var(--nav-sky-light)',
                'nav-sky-dark': 'var(--nav-sky-dark)',
                'nav-sky-text-dark': 'var(--nav-sky-text-dark)',
            },
            minHeight: {
                'screen-footer': 'calc(100vh - 9rem)', // calcul de la hauteur du footer
            },
            fontFamily: {
                'rubik': ['rubik'],
            },
        },
    },
    plugins: [
        require('flowbite/plugin')
    ],
}
