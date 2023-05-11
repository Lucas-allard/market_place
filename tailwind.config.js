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
      'custom-blue': '#5C74A6',
      'custom-blue-darker': '#3F4F7F'
    },
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')
  ]
}

