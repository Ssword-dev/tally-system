import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        index: resolve(__dirname, 'resources/js/index.ts'),
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: '[name].js',
      },
    },
    outDir: 'public/static/js'
  },
  server: {
    origin: 'http://localhost:5173',
    fs: {
      strict: false
    }
  }
})
