{{-- Stats / Números --}}
<section id="sobre" class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
    <div class="relative rounded-2xl border border-white/10 bg-white/3 backdrop-blur-xl p-12 lg:p-20 overflow-hidden"
        data-aos="fade-up" data-aos-duration="1000">
        {{-- Glow top --}}
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-32 bg-emerald/20 blur-[100px] rounded-full">
        </div>

        <div class="text-center max-w-2xl mx-auto mb-16 relative z-10">
            <h2 class="font-display text-4xl font-extrabold text-white mb-4">Nossos Números</h2>
            <p class="text-muted text-lg">que demonstram os objetivos do LabSIS com resultados.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10 relative z-10">
            <div class="text-center">
                <i data-lucide="hammer" class="w-10 h-10 text-emerald mx-auto mb-4"></i>
                <p class="text-5xl md:text-6xl font-display font-black text-white mb-2">{{ $totalAllProjects }}</p>
                <p class="text-xs font-bold text-muted uppercase tracking-widest">Projetos em avaliação</p>
            </div>
            <div class="text-center">
                <i data-lucide="package-check" class="w-10 h-10 text-rose mx-auto mb-4"></i>
                <p class="text-5xl md:text-6xl font-display font-black text-white mb-2">{{ $totalProjects }}</p>
                <p class="text-xs font-bold text-muted uppercase tracking-widest">Projetos Entregues</p>
            </div>
            <div class="text-center">
                <i data-lucide="users" class="w-10 h-10 text-emerald mx-auto mb-4"></i>
                <p class="text-5xl md:text-6xl font-display font-black text-white mb-2">{{ $totalAllDevelopers }}</p>
                <p class="text-xs font-bold text-muted uppercase tracking-widest">Devs Envolvidos</p>
            </div>
        </div>
    </div>
</section>
