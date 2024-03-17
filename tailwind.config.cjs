const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './resources/web-app/**/*.{vue,js,ts}',
    './resources/views/web-app.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [],
};
