/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js"

    ],
    theme: {
        fontFamily: {
            'dm-sans': ['DM Sans', 'sans-serif'],
        },
        colors: {
            'custom-dark-blue': '#1F2E4D',
            'custom-blue': '#5C74A6',
            'custom-blue-darker': '#3F4F7F',
            'custom-light-blue': '#C8DCE8',
            'custom-light-blue-darker': '#A6CCE3',
            'custom-light-green': '#C4E3D6',
            'custom-light-green-darker': '#A2D0BD',
            'custom-green': '#4D9F7F',
            'custom-light-cream': '#FFF3EA',
            'custom-light-cream-darker': '#F5E2D3',
            'custom-light-pink': '#fce8ef',
            'custom-light-pink-darker': '#EFD5DE',
            'custom-pink': '#F9C5D1',
            'custom-pink-darker': '#E6AEBB',
            'custom-gray': '#E6E6E6',
            'custom-gray-darker': '#C2C2C2',
        },
        extend: {},
    },
    plugins: [
        require('flowbite/plugin')
    ]
}

