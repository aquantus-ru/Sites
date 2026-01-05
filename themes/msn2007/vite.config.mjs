import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const defaultOutDir = 'assets/dist';

export default defineConfig({
    base: `${process.env.VITE_BASE}/${defaultOutDir}`,
    build: {
        outDir: defaultOutDir,
        assetsDir: '',
    },
    plugins: [
        laravel({
            publicDirectory: defaultOutDir,
            input: [
                'assets/src/css/theme-msn2007.css',
                'assets/src/js/theme-msn2007.js',
            ],
            refresh: {
                paths: [
                    './**/*.htm',
                    './**/*.block',
                    'assets/src/**/*.css',
                    'assets/src/**/*.js',
                    'assets/src/**/*.jsx',
                ]
            },
        }),
    ],
});
