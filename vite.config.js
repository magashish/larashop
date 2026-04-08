import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // publicDir: 'public', // This is the default, so it's often not explicitly needed.
                          // It tells Vite where your public assets (like index.html in a SPA,
                          // or images you want directly copied) are.
                          // For Laravel, the public folder is usually handled by the web server.

    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/slick.css',
                'resources/css/style.css',
                'resources/css/custom.css', // Added 'custom.css' - good!
                'resources/css/external.css',
                'resources/js/app.js',
                'resources/js/flipclock.js',
                'resources/js/consumer.js',
            ],
            refresh: true, // This is primarily for HMR in development.
        }),
    ],
    // No 'build' options explicitly defined, so Vite uses its defaults:
    // outDir: 'public/build'
    // assetsDir: 'assets'
    // manifest: true
});