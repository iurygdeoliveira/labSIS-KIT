<header id="header" x-data="{ navigationOpen: false }"
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
                    ['label' => 'Acessar a Plataforma', 'url' => url('/login')],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ $link['url'] }}"
                    class="text-gray-700 transition-colors duration-700 dark:text-gray-300 hover:text-teal-600 dark:hover:text-teal-400">
                    {{ $link['label'] }}
                </a>
            @endforeach

            
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
        </nav>
    </div>
</header>
