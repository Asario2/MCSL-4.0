import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import path from 'path'

export default defineConfig(() => {
    return {
        plugins: [
            vue(),

            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],

                ssr: 'resources/js/ssr.js',

                refresh: true,
            }),
        ],

        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),

                photoswipe: path.resolve(
                    __dirname,
                    'node_modules/photoswipe'
                ),
            },
        },

        server: {
            cors: true,

            hmr: {
                host: 'localhost',
            },
        },

        build: {
            sourcemap: true,
        },

        ssr: {
            noExternal: [
                '@inertiajs/server',
                '@inertiajs/vue3',
            ],
        },

        optimizeDeps: {
            include: [
                'photoswipe',
            ],
        },
    }
})
