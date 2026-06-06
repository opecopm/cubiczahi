import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/views/themes/laundry-one/assets/css/app.css',
                'resources/views/themes/laundry-one/assets/js/app.js',
                'resources/views/themes/supermarket/assets/css/app.css',
                'resources/views/themes/supermarket/assets/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
