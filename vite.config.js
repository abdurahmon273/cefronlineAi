import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/test/reading/reading.css',
                'resources/js/test/reading/reading.js',
                'resources/js/test/reading_result/reading_result.js',
                'resources/css/test/writing/writing.css',
                'resources/js/test/writing/writing.js',
                'resources/css/test/listening/listening.css',
                'resources/js/test/listening/listening.js',
                'resources/js/test/listening_result/listening_result.js',
                'resources/css/test/speaking/speaking.css',
                'resources/js/test/speaking/speaking.js',
            ],

            refresh: true,
        }),
    ],
});
