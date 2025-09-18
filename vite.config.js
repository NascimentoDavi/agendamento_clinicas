import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import fg from 'fast-glob'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...fg.sync('resources/css/**/*.css'),
                ...fg.sync('resources/js/**/*.js'),
            ],
            refresh: true,
        }),
    ],
})
