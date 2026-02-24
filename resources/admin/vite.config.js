import { defineConfig } from 'vite'
import path from 'path'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(),
     tailwindcss()
  ],
  resolve: {
    alias: {
      // point the 'firebase' package to a local empty stub so Rollup/Vite
      // won't try to resolve the node_modules 'firebase' package which
      // may have problematic exports for this build environment.
      'firebase': path.resolve(__dirname, 'src/firebase-empty.js')
    }
  },
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
   // avoid pre-bundling firebase packages which may cause resolution errors
})
