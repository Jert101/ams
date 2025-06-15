import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/pwa.js'
            ],
            refresh: true,
            publicDirectory: 'public',
            buildDirectory: 'build',
            manifest: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        base: process.env.ASSET_URL || '/',
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    pwa: ['resources/js/pwa.js'],
                },
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            },
        },
        sourcemap: true,
        manifest: true,
    },
});
