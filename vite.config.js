import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({

    server: {
        cors: {
            origin: [
                /^https?:\/\/(?:(?:[^:]+\.)?localhost|127\.0\.0\.1|\[::1\])(?::\d+)?$/,
                /^https?:\/\/.*\.test(:\d+)?$/,          // Valet / Herd    (SCHEME://*.test:PORT)
            ],
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
