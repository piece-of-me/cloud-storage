/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      letterSpacing: {
        narrowly: '-0.075em',
      },
    },
  },
  plugins: [],
}
