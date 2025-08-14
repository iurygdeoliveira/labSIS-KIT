<header id="header" x-data="{ navigationOpen: false, darkMode: $persist(false).as('dark-mode') }" x-init="$watch('darkMode', value => document.documentElement.classList.toggle('dark', value))"
    class="fixed top-0 left-0 z-50 w-full transition-colors duration-700 bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700">

    <div class="flex items-center justify-between h-16 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ url('/') }}"
            class="text-2xl font-bold transition-colors duration-700 text-teal-600 dark:text-teal-400">
            {{ config('app.name', 'Freelancy') }}
        </a>

        {{-- Navegação Desktop --}}
        <nav class="items-center hidden space-x-6 md:flex">
            @php
                $links = [
                    ['label' => 'Início', 'url' => url('/')],
                    ['label' => 'Sobre', 'url' => url('/#services')],
                    ['label' => 'Serviços', 'url' => url('/#services')],
                    ['label' => 'Contato', 'url' => url('/#services')],
                    ['label' => 'Acessar a Plataforma', 'url' => filament()->getLoginUrl()],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ $link['url'] }}"
                    class="text-gray-700 transition-colors duration-700 dark:text-gray-300 hover:text-teal-600 dark:hover:text-teal-400">
                    {{ $link['label'] }}
                </a>
            @endforeach

            {{-- Toggle Tema (Desktop) --}}
            <div class="flex items-center space-x-2">
                <flux:button x-data x-on:click="darkMode = !darkMode" icon="moon" variant="subtle"
                    aria-label="Toggle dark mode" />
            </div>
        </nav>

        {{-- Botão Mobile Toggle --}}
        <div class="flex md:hidden">
            <button @click="navigationOpen = !navigationOpen"
                class="text-gray-700 transition-colors duration-700 rounded dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <template x-if="!navigationOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </template>
                <template x-if="navigationOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
            </button>
        </div>
    </div>

    {{-- Menu Mobile --}}
    <div x-show="navigationOpen" x-transition
        class="transition-all duration-300 bg-white border-t border-gray-200 md:hidden dark:bg-gray-900 dark:border-gray-700">
        <nav class="flex flex-col p-4 space-y-2">
            @foreach ($links as $link)
                <a href="{{ $link['url'] }}"
                    class="block py-2 text-gray-700 transition-colors duration-700 dark:text-gray-300 hover:text-teal-600 dark:hover:text-teal-400">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <button @click="darkMode = !darkMode"
                    class="text-gray-600 transition-colors duration-700 dark:text-gray-300 hover:text-teal-500">
                    <template x-if="!darkMode">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07 5.66l-.7-.7M6.34 6.34l-.7-.7m12.02 0l-.7.7M6.34 17.66l-.7.7M12 5a7 7 0 110 14a7 7 0 010-14z" />
                        </svg>
                    </template>
                    <template x-if="darkMode">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 116.707 2.707 7 7 0 1017.293 13.293z" />
                        </svg>
                    </template>
                </button>
            </div>
        </nav>
    </div>
</header>
