{{-- Projetos Estáticos --}}
@php
    $staticProjects = config('landing-projects', []);

    $categoryConfig = [
        'Landing Page' => ['icon' => 'globe',       'color' => 'emerald'],
        'SaaS'         => ['icon' => 'cloud-cog',    'color' => 'emerald'],
        'Mobile'       => ['icon' => 'smartphone',   'color' => 'rose'],
        'Bot'          => ['icon' => 'bot',           'color' => 'rose'],
    ];
@endphp

@if(count($staticProjects) > 0)
<section id="projetos" class="w-full py-12 relative z-10 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8" data-aos="fade-up">
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 class="font-display text-3xl md:text-5xl font-bold tracking-tight mb-4">
                    Nossos <span class="text-gradient-emerald">Projetos</span>
                </h2>
                <p class="text-muted text-lg max-w-xl">Sistemas reais construídos pelos membros do LabSIS, em
                    produção e gerando impacto.</p>
            </div>
            @if(count($staticProjects) > 3)
            <div class="hidden md:flex items-center gap-3">
                <button onclick="scrollCarousel(-1)"
                    class="w-10 h-10 rounded-full border border-border bg-card hover:bg-white/10 flex items-center justify-center text-white transition-colors cursor-pointer"
                    aria-label="Anterior">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button onclick="scrollCarousel(1)"
                    class="w-10 h-10 rounded-full border border-border bg-card hover:bg-white/10 flex items-center justify-center text-white transition-colors cursor-pointer"
                    aria-label="Próximo">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    <div id="projects-carousel"
        class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth px-4 sm:px-6 lg:px-8 pt-6 pb-8 no-scrollbar max-w-7xl mx-auto"
        data-aos="fade-up" data-aos-delay="100">

        @foreach($staticProjects as $index => $project)
            @php
                $cat = $categoryConfig[$project['category']] ?? ['icon' => 'folder', 'color' => 'emerald'];
                $isEven = $index % 2 === 0;
                $accentColor = $isEven ? 'emerald' : 'rose';
                $glowClass = $isEven ? 'glow-emerald' : 'glow-rose';
            @endphp

            <a href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer"
               class="snap-start shrink-0 w-[300px] md:w-[340px] group cursor-pointer flex flex-col">
                <div class="flex-1 w-full rounded-xl bg-card border border-border group-hover:border-emerald transition-all duration-300 flex flex-col overflow-hidden relative shadow-lg hover:shadow-emerald/5 hover:-translate-y-1">
                    
                    {{-- Logo do Projeto --}}
                    <div class="h-40 bg-base flex items-center justify-center border-b border-border p-6">
                        @if(!empty($project['logo']))
                            <div class="rounded-xl overflow-hidden shadow-lg shadow-black/30 ring-1 ring-white/10">
                                <img
                                    src="{{ asset($project['logo']) }}"
                                    alt="Logo {{ $project['name'] }}"
                                    class="max-h-28 w-auto object-contain rounded-xl"
                                >
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-{{ $accentColor }}/10 border border-{{ $accentColor }}/20 flex items-center justify-center">
                                <i data-lucide="{{ $cat['icon'] }}" class="w-10 h-10 text-{{ $accentColor }}"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-6 flex flex-col gap-3 relative z-10 items-center text-center grow">
                        {{-- Categoria (badge centralizado) --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-{{ $cat['color'] }}/10 border border-{{ $cat['color'] }}/20 text-{{ $cat['color'] }}">
                            <i data-lucide="{{ $cat['icon'] }}" class="w-3 h-3"></i>
                            {{ $project['category'] }}
                        </span>

                        {{-- Descrição completa (máx 300 caracteres) --}}
                        <p class="text-muted text-sm leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($project['description'], 300) }}
                        </p>

                        {{-- Link --}}
                        <div class="flex items-center gap-2 text-{{ $accentColor }} text-sm font-semibold mt-auto group-hover:gap-3 transition-all pt-4">
                            Visitar projeto <i data-lucide="external-link" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

    </div>
</section>

@push('scripts')
<script>
    function scrollCarousel(direction) {
        const carousel = document.getElementById('projects-carousel');
        const firstCard = carousel.querySelector('a');
        if (!firstCard) return;
        const cardWidth = firstCard.offsetWidth + 24;
        carousel.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
    }
</script>
@endpush
@endif
