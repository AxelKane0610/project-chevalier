import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js', 
                'resources/js/new-ticket.js', 
                'resources/js/software-ticket-details.js', 
                'resources/js/laser-engraving.js', 
                'resources/js/thermal-event.js',
                'resources/css/icons/themify-icons.css',
                'resources/js/invoice-exceptional.js',
                'resources/js/out-of-office.js'
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
