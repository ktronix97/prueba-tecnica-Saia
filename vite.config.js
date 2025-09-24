import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  publicDir: 'static', 
  build: {
    outDir: 'public/build',
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'assets/js/main.js'),
        auth: resolve(__dirname, 'assets/js/auth.js'),
        dashboard: resolve(__dirname, 'assets/js/dashboard.js')
      },
      output: {
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      }
    }
  }
})

