<footer class="text-white transition-colors duration-700 bg-emerald-900">
    <div class="px-4 py-16 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid gap-8 text-center lg:text-left lg:grid-cols-4">
            {{-- Logo e Descrição --}}
            <div class="lg:col-span-2">
                <div class="flex items-center justify-center mb-4 space-x-2 lg:justify-start">
                    <span class="flex items-center">
                        {{-- Lab SVG --}}
                        <img src="{{ asset('images/Lab_footer.svg') }}" alt="Lab" class="w-auto h-5 sm:h-6 lg:h-8 max-w-none" style="min-width: 45px;">
                        
                        {{-- SIS SVG --}}
                        <img src="{{ asset('images/SIS_footer.svg') }}" alt="SIS" class="w-auto h-6 ml-1 sm:h-8 lg:h-9 max-w-none" style="min-width: 45px;">
                    </span>
                </div>
                <p class="max-w-md mx-auto mb-6 text-gray-100 lg:mx-0">
                    Laboratório de Sistemas Inovadores.
                </p>
                <div class="flex items-center justify-center text-gray-100 lg:justify-start">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm">Instituto Federal do Tocantins - Campus Araguaína, TO</span>
                </div>
            </div>

            {{-- Links Rápidos --}}
            <div class="text-center lg:text-left">
                <h3 class="mb-4 text-lg font-semibold">Links Rápidos</h3>
                <ul class="space-y-2">
                    <li><a href="#sobre" class="text-gray-100 transition-colors hover:text-[#D93223]">Sobre</a></li>
                    <li><a href="#projetos" class="text-gray-100 transition-colors hover:text-[#D93223]">Projetos</a></li>
                    <li><a href="#contato" class="text-gray-100 transition-colors hover:text-[#D93223]">Contato</a></li>
                </ul>
            </div>

            {{-- Contato --}}
            <div class="text-center lg:text-left">
                <h3 class="mb-4 text-lg font-semibold">Contato</h3>
                <ul class="space-y-2 text-gray-100">
                    <li class="flex items-center justify-center lg:justify-start">
                        <svg class="w-4 h-4 mr-2 text-gray-100" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        em breve ...
                    </li>
                </ul>
                
                {{-- Redes Sociais --}}
                <div class="mt-4">
                    <h4 class="mb-3 text-sm font-semibold text-gray-200">Redes Sociais</h4>
                    <div class="flex justify-center space-x-3 lg:justify-start">
                        {{-- GitHub --}}
                        <a href="#" class="text-gray-300 transition-colors hover:text-[#D93223]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                        
                        {{-- LinkedIn --}}
                        <a href="#" class="text-gray-300 transition-colors hover:text-[#D93223]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        
                        {{-- Instagram --}}
                        <a href="#" class="text-gray-300 transition-colors hover:text-[#D93223]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="pt-8 mt-8 border-t border-emerald-800">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div class="text-sm text-center text-gray-200 md:text-left">
                    © {{ now()->year }} LabSIS – Laboratório de Sistemas Inovadores. Todos os direitos reservados.
                </div>
                <div class="text-sm text-gray-200">
                    Desenvolvido por <a href="https://github.com/iurygdeoliveira" target="_blank" class="transition-colors hover:text-[#D93223]">Iury Gomes de Oliveira</a>
                </div>
            </div>
        </div>
    </div>
</footer>
