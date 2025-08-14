<footer class="transition-colors duration-700 bg-white border-t border-gray-200 dark:bg-gray-900 dark:border-gray-700">
    <div
        class="flex flex-col items-center justify-between gap-4 px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8 md:flex-row">

        {{-- Copyright --}}
        <div class="text-sm text-center text-gray-600 dark:text-gray-400 md:text-left">
            © {{ now()->year }} {{ config('app.name', 'Freelancy') }}. Todos os direitos reservados.
        </div>

        {{-- Redes sociais --}}
        <div class="flex items-center space-x-6">
            {{-- Facebook --}}
            <a href="#"
                class="text-gray-500 transition-colors hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M22 12a10 10 0 10-11.6 9.8v-6.9h-2.1V12h2.1V9.7c0-2.1 1.2-3.3 3.1-3.3.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.2l-.3 2.9h-1.9v6.9A10 10 0 0022 12z" />
                </svg>
            </a>

            {{-- Twitter --}}
            <a href="#"
                class="text-gray-500 transition-colors hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M23 3a10.9 10.9 0 01-3.14 1.53A4.48 4.48 0 0012 8v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c13 8 27 0 27-16a10.2 10.2 0 00-.08-1.73A7.72 7.72 0 0023 3z" />
                </svg>
            </a>

            {{-- Adicione mais ícones sociais aqui conforme necessário --}}
        </div>
    </div>
</footer>
