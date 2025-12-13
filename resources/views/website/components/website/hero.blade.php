{{-- Hero Section --}}
<section class="relative mx-2 my-4 overflow-hidden bg-linear-to-br from-emerald-900 via-emerald-700 to-emerald-900 rounded-3xl sm:mx-4 sm:my-8">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23F2F2F0\' fill-opacity=\'0.4\'%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'2\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="relative px-3 mx-auto max-w-7xl sm:px-4 lg:px-8">
        <div class="py-12 sm:py-16 lg:py-32">
            <div class="grid items-center gap-6 sm:gap-8 lg:gap-12 lg:grid-cols-2">
                {{-- Visual Element (Primeiro em telas menores) --}}
                <div class="relative order-1 lg:order-2">
                    <div class="relative z-10 w-full max-w-sm mx-auto sm:max-w-lg">
                        <div class="relative">
                            <div class="absolute w-48 h-48 rounded-full -bottom-4 -right-4 bg-emerald-600 mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000 sm:-bottom-8 sm:-right-8 sm:w-72 sm:h-72"></div>
                            <div class="relative">
                                <img src="{{ asset('images/LabSIS.png') }}" alt="LabSIS Logo" class="w-full h-auto shadow-2xl rounded-2xl" style="max-width: 100%; height: auto; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Content (Segundo em telas menores) --}}
                <div class="text-center lg:text-left order-2 lg:order-1">
                    <div class="flex items-center justify-center mb-6 sm:mb-8 lg:justify-start">
                        
                        
                    </div>
                    <h2 class="mb-4 text-xl font-bold text-gray-100 sm:text-2xl sm:mb-6 sm:text-3xl md:text-3xl lg:text-2xl xl:text-3xl sm:whitespace-nowrap">
                        Laboratório de Sistemas Inovadores
                    </h2>
                    <p class="max-w-2xl mx-auto mb-6 text-base text-gray-100 sm:mb-8 sm:text-lg lg:mx-0">
                        Transformamos desafios reais em soluções inteligentes. Criamos sistemas que otimizam seus processos e resolvem seus problemas.
                    </p>
                    <div class="flex flex-col gap-3 sm:gap-4 sm:flex-row sm:justify-center lg:justify-start lg:flex-wrap">
                        <a href="#projetos" class="inline-flex items-center justify-center px-4 py-3 text-sm font-medium transition-colors duration-200 bg-gray-100 border border-transparent rounded-lg text-emerald-900 hover:bg-gray-200 sm:px-6 sm:py-4 sm:text-base lg:px-8 lg:text-lg">
                            <svg class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                            Ver Projetos
                        </a>
                        <a href="https://www.instagram.com/labsis.dev/" class="inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-gray-100 transition-colors duration-200 border-2 border-gray-100 rounded-lg hover:bg-gray-100 hover:text-emerald-900 sm:px-6 sm:py-4 sm:text-base lg:px-8 lg:text-lg">
                            <svg class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Participe do LabSIS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
