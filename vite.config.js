import { defineConfig, lazyPlugins } from "vite-plus";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    staged: {
        "*": "vp check --fix",
    },
    fmt: {
        ignorePatterns: [
            "docs/**",
            "public/**",
            "storage/**",
            "vendor/**",
            "resources/views/**",
            "lang/**",
            "report.html",
            "composer.json",
            "boost.json",
        ],
    },
    lint: {
        ignorePatterns: [
            "docs/**",
            "public/**",
            "storage/**",
            "vendor/**",
            "resources/views/**",
            "lang/**",
            "report.html",
            "composer.json",
            "boost.json",
        ],
        jsPlugins: [{ name: "vite-plus", specifier: "vite-plus/oxlint-plugin" }],
        rules: { "vite-plus/prefer-vite-plus-imports": "error" },
        options: { typeAware: true, typeCheck: true },
    },
    plugins: lazyPlugins(() => [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/filament/admin/theme.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ]),
});
