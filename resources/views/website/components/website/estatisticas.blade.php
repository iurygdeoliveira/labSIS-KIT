{{-- Estatísticas Section --}}
@php
    // Estatísticas fictícias para demonstração
    $totalProjects    = 15;
    $totalDevelopers  = 42;
    $totalAdvisors    = 8;
@endphp

<section class="py-24 mx-4 my-8 bg-linear-to-br from-emerald-900 via-emerald-700 to-emerald-900 rounded-3xl">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Nossos Números
            </h2>
            <p class="max-w-2xl mx-auto mt-6 text-lg text-gray-100">
                Resultados que demonstram nosso compromisso com a excelência e inovação em software.
            </p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-4 mt-16 sm:gap-8">
            {{-- Total de Projetos --}}
            <div x-data="{ count: 0, target: {{ $totalProjects }} }" x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const timer = setInterval(() => {
                                if (count < target) {
                                    count++;
                                } else {
                                    clearInterval(timer);
                                }
                            }, 100);
                        }
                    });
                });
                observer.observe($el);
            " class="flex-1 min-w-0 text-center">
                <div class="relative">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full sm:w-20 sm:h-20">
                        <img src="{{ asset('images/project.svg') }}" alt="Projetos" class="w-8 h-8 sm:w-10 sm:h-10">
                    </div>
                    <div class="text-3xl font-bold text-white sm:text-4xl" x-text="count">0</div>
                    <div class="mt-2 text-sm font-medium text-gray-100 sm:text-lg">Projetos Desenvolvidos</div>
                </div>
            </div>
            
            {{-- Total de Desenvolvedores --}}
            <div x-data="{ count: 0, target: {{ $totalDevelopers }} }" x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const timer = setInterval(() => {
                                if (count < target) {
                                    count++;
                                } else {
                                    clearInterval(timer);
                                }
                            }, 150);
                        }
                    });
                });
                observer.observe($el);
            " class="flex-1 min-w-0 text-center">
                <div class="relative">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full sm:w-20 sm:h-20">
                        <img src="{{ asset('images/developer.svg') }}" alt="Desenvolvedores" class="w-8 h-8 sm:w-10 sm:h-10">
                    </div>
                    <div class="text-3xl font-bold text-white sm:text-4xl" x-text="count">0</div>
                    <div class="mt-2 text-sm font-medium text-gray-100 sm:text-lg">Desenvolvedores Participantes</div>
                </div>
            </div>
            
            {{-- Orientadores de Projetos --}}
            <div x-data="{ count: 0, target: {{ $totalAdvisors }} }" x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const timer = setInterval(() => {
                                if (count < target) {
                                    count++;
                                } else {
                                    clearInterval(timer);
                                }
                            }, 200);
                        }
                    });
                });
                observer.observe($el);
            " class="flex-1 min-w-0 text-center">
                <div class="relative">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full sm:w-20 sm:h-20">
                        <img src="{{ asset('images/advisor.svg') }}" alt="Orientadores" class="w-8 h-8 sm:w-10 sm:h-10">
                    </div>
                    <div class="text-3xl font-bold text-white sm:text-4xl" x-text="count">0</div>
                    <div class="mt-2 text-sm font-medium text-gray-100 sm:text-lg">Orientadores de Projetos</div>
                </div>
            </div>
        </div>
        
    </div>
</section> 