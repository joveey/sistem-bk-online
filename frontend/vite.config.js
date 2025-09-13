import { fileURLToPath, URL } from 'node:url'
import path from 'path' // Pastikan 'path' diimpor
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      // Alias default untuk @/ mengarah ke src/
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      
      // --- ALIAS UNTUK SHADCN ADA DI SINI ---
      // Ini memberitahu Vite di mana menemukan folder components dan lib
      '@/components': path.resolve(__dirname, './src/components'),
      '@/lib': path.resolve(__dirname, './src/lib'),
    }
  }
})

