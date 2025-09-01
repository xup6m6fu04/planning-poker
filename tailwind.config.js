/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['LXGWWenKaiTC', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        'mono': ['LXGWWenKaiMonoTC', 'ui-monospace', 'SFMono-Regular', 'monospace'],
        'wenkai': ['LXGWWenKaiTC', 'sans-serif'],
        'wenkai-mono': ['LXGWWenKaiMonoTC', 'monospace'],
      },
    },
  },
  plugins: [],
}