import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(),
     tailwindcss()
  ],
  base: '/admin/',
   build: {
     outDir: '../../public/admin',
    emptyOutDir: true,
    sourcemap: true,
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom', 'react-router-dom'],
          firebase: ['firebase'],
          ui: ['lucide-react', 'react-hot-toast'],
        }
      }
    }
  },
   optimizeDeps: {
    include: ["firebase/app"]
  }
})
