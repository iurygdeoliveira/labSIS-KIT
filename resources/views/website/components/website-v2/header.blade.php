{{-- Header --}}
<header class="fixed top-0 w-full z-50 glass-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-end h-16 relative">

            {{-- Desktop Nav --}}
            <nav class="hidden lg:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center gap-8 bg-white/5 rounded-full px-8 py-2.5 border border-white/10"
                data-aos="fade-down" data-aos-duration="800" data-aos-delay="100">
                <a href="#sobre" class="text-muted hover:text-white transition-colors text-sm font-semibold">Sobre o
                    LabSIS</a>
                <a href="#diferenciais"
                    class="text-muted hover:text-white transition-colors text-sm font-semibold">Como Funciona</a>
                <a href="#ecossistema"
                    class="text-muted hover:text-white transition-colors text-sm font-semibold">Ecossistema</a>
            </nav>

            {{-- CTA --}}
            <div class="flex items-center gap-4" data-aos="fade-down" data-aos-duration="800" data-aos-delay="200">
                <a href="{{ url('/login') }}"
                    class="hidden sm:inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-[#080808] bg-emerald hover:bg-emerald-hover rounded-full transition-all shadow-glow-emerald hover:-translate-y-0.5 cursor-pointer">
                    Acessar Plataforma
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
                {{-- Mobile menu button --}}
                <button id="mobile-menu-btn" class="lg:hidden text-white/70 hover:text-white cursor-pointer" onclick="toggleMobileMenu()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden lg:hidden pb-4">
            <nav class="flex flex-col gap-3 bg-white/5 rounded-xl px-6 py-4 border border-white/10">
                <a href="#sobre" class="text-muted hover:text-white transition-colors text-sm font-semibold py-2">Sobre o LabSIS</a>
                <a href="#diferenciais" class="text-muted hover:text-white transition-colors text-sm font-semibold py-2">Como Funciona</a>
                <a href="#ecossistema" class="text-muted hover:text-white transition-colors text-sm font-semibold py-2">Ecossistema</a>
                <a href="{{ url('/login') }}"
                    class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-[#080808] bg-emerald hover:bg-emerald-hover rounded-full transition-all mt-2 cursor-pointer">
                    Acessar Plataforma
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
            </nav>
        </div>
    </div>
</header>

@push('scripts')
<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
</script>
@endpush
