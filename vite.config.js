import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        proxy: {
            // 只代理 API 請求和 Laravel 路由到後端
            '/api': {
                target: 'https://planning-poker-docker.bookwalker.com.tw',
                changeOrigin: true,
                secure: true,
            },
            // 代理非 Vite 資源的請求
            '^(?!/(@vite|node_modules|resources|assets)/).*\\.(php|html)$': {
                target: 'https://planning-poker-docker.bookwalker.com.tw',
                changeOrigin: true,
                secure: true,
            }
        }
    },
});
