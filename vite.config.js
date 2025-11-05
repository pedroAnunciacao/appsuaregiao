import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            // input: ['resources/js/main.js', 'resources/js/style.css'],
            input: ['resources/js/app.js', 'resources/js/style.css', 'resources/css/app.css'],

            refresh: true,
        }),
    ],

    // 🔧 Ambiente de desenvolvimento (local)
    server: {
        proxy: {
            '/api': 'http://127.0.0.1:8000',
        },
        host: '0.0.0.0',
        port: 5173,
    },

    // 🌐 Configurações para produção
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});

