import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import cssInjectedByJsPlugin from 'vite-plugin-css-injected-by-js'
import path from 'path'

// https://vitejs.dev/config/

export default defineConfig({
  plugins: [
    react(),
    cssInjectedByJsPlugin()
  ],
  server: {
    origin: 'http://localhost:5173',
    port: 5173,
    cors: true,
    hmr: {
      host: 'localhost',
      protocol: 'ws'
    }
  },
  resolve: { alias: { path: 'path-browserify' } },
  publicDir: 'public',
  base: '/blocks/cursos_slider/react/app/',
  build: {
    outDir: path.resolve(__dirname, 'dist'),
    cssCodeSplit: false, // Habilita o no (true/false) la división de código para CSS
    manifest: true,
    assetsDir: 'assets',

    // Configuración específica para Rollup
    rollupOptions: {
      output: {
        format: 'iife', // Importante: usar IIFE para evitar problemas con el scope
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
      }
    }
  },
  define: {
    // Definir variables globales que Moodle normalmente proporciona
    'process.env': {
      NODE_ENV: JSON.stringify(process.env.NODE_ENV)
    }
  }
});