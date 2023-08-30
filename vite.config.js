import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { viteStaticCopy } from "vite-plugin-static-copy";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/mock/styles.scss", "resources/mock/script.js"],
            refresh: true,
        }),

        viteStaticCopy({
            targets: [
                {
                    src: "node_modules/tinymce/*",
                    dest: "tinymce",
                },
                {
                    src: "node_modules/@yaireo/tagify/*",
                    dest: "tagify",
                },
                {
                    src: "resources/mock/images/*",
                    dest: "images",
                },
            ],
        }),
    ],
    resolve: {
        alias: {
            $: "jQuery",
            '~fontawesome': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free'), // <- add this line
        },
    },
});
