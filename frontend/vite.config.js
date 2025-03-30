import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

// Configuração para o Vite
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),  // Alias '@' aponta para a pasta 'src'
    },
  },
  build: {
    rollupOptions: {
      external: ['laravel-echo', 'pusher-js'],
    }
  },
});