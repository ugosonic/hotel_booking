import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/universal.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build', // Ensure assets build into public/build
        manifest: true,
        rollupOptions: {
            input: [
                'resources/css/app.css',
                'resources/css/universal.css',
                'resources/js/app.js'
            ],
        },
    },
});
