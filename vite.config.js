import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tsconfigPaths from 'vite-tsconfig-paths';
import path from 'path';

if (process.env.NODE_ENV === 'production') {
  process.env.VITE_APP_BUILD_EPOCH = new Date().getTime().toString();
}

export default defineConfig({
  resolve: {
    alias: [
      { find: '@', replacement: path.resolve(__dirname, 'resources/web-app') },
    ],
  },
  plugins: [
    vue(),
    tsconfigPaths(),
    laravel({
      input: ['resources/web-app/app.ts'],
      refresh: true,
    }),
  ],
});
