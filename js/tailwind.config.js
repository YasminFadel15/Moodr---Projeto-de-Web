/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: ["./**/*.php", "./**/*.html", "./**/*.js"],
  theme: {
    extend: {
      colors: {
        'purple-primary': '#7357C0',
        'purple-medium': '#8F6FE5',
        'purple-light': '#C194ED',
        'purple-dark': '#544E7E',
        'purple-medium-2': '#9487B5',
        'purple-light-2': '#C1ACDD',
        'purple-dark-2': '#423C52',

        'gray-primary': '#9695AB',
        'gray-light': '#D1CFE5',

        'white-primary': '#F9F8FF',

        'red-negative': '#E64848',
        'green-positive': '#3FCF92',
      },
    },
  },
  plugins: [],
}
