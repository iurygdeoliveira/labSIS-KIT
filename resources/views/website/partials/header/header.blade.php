<header id="header" x-data="{ navigationOpen: false, darkMode: false }" 
    x-init="
        darkMode = false;
        localStorage.removeItem('dark-mode');
        document.documentElement.classList.remove('dark');
        $watch('darkMode', value => document.documentElement.classList.toggle('dark', value))
    "
    class="fixed top-0 left-0 z-50 w-full transition-colors duration-700 bg-white border-b border-gray-200 ">

    <div class="flex items-center justify-between h-16 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ url('/') }}"
            class="flex items-center transition-colors duration-700">
           
            <span class="flex items-center">
                {{-- LabSIS SVG Unificado --}}
                <img src="{{ asset('images/LabSIS.svg') }}" alt="LabSIS" class="w-auto h-6 lg:h-9 max-w-none" style="min-width: 95px;">
            </span>
        </a>

        {{-- Navegação Desktop --}}
        <nav class="items-center hidden space-x-6 md:flex">
            @php
                $links = [
                    ['label' => 'Início', 'url' => url('/')],
                    ['label' => 'Sobre', 'url' => url('/#sobre')],
                    ['label' => 'Projetos', 'url' => url('/#projetos')],
                    //['label' => 'Blog', 'url' => route('blog.index')],
                    ['label' => 'Acessar Plataforma', 'url' => url('/login')],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ $link['url'] }}"
                    class="font-medium text-gray-700 transition-colors duration-700 hover:text-[#D93223]">
                    {{ $link['label'] }}
                </a>
            @endforeach

        </nav>

        {{-- Botão Mobile Toggle --}}
        <div class="flex md:hidden">
            <button @click="navigationOpen = !navigationOpen"
                class="text-gray-700 transition-colors duration-700 rounded focus:outline-none focus:ring-2 focus:ring-green-600">
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
        class="transition-all duration-300 bg-white border-t border-gray-200 md:hidden ">
        <nav class="flex flex-col p-4 space-y-2">
            @foreach ($links as $link)
                <a href="{{ $link['url'] }}"
                    class="block py-2 font-medium text-gray-700 transition-colors duration-700 hover:text-green-600">
                    {{ $link['label'] }}
                </a>
            @endforeach
            
        </nav>
    </div>
</header>
