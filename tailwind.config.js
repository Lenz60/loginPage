/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/*.php",
    "./app/Views/**/*.php",
    "./app/Views/**/**/*.php",
    "./app/Views/**/**/**/*.php",
    "./app/Views/**/**/**/**/*.php",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

