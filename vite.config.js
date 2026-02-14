import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/css/app-layout.css',
                'resources/css/delivery.css',
                'resources/css/warehouse.css',
                'resources/css/employees.css',
                'resources/css/employees-form.css',
                'resources/css/inventory.css',
                'resources/css/inventory-form.css',
                'resources/css/billing.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/app-layout.js',
                'resources/js/delivery.js',
                'resources/js/warehouse.js',
                'resources/js/employees.js',
                'resources/js/employees-form.js',
                'resources/js/inventory.js',
                'resources/js/inventory-form.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
