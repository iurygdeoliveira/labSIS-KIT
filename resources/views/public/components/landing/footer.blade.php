{{-- Footer --}}
<footer class="border-t border-border bg-[#050505] py-16 relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
        {{-- Brand --}}
        <div class="md:col-span-2 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-2 mb-6 group">
                <img src="{{ asset('images/labsis_logo_bg.png') }}" alt="LabSIS Logo"
                    class="h-12 md:h-14 w-auto max-w-[min(100%,22rem)] opacity-90 group-hover:opacity-100 group-hover:drop-shadow-[0_0_15px_rgba(16,185,129,0.4)] transition-all duration-300">
            </div>
            <p class="text-muted text-sm leading-relaxed max-w-md mx-auto md:mx-0 mb-6">
                Ambiente de Inovação do Campus Araguaína - IFTO, voltado à criação de
                Software.
            </p>
        </div>

        {{-- Nav Links --}}
        <div class="text-center md:text-left">
            <h4 class="font-display text-white font-bold uppercase tracking-widest text-xs mb-6">Navegação</h4>
            <ul class="flex flex-col gap-3">
                <li><a href="#diferenciais"
                        class="text-muted hover:text-emerald text-sm transition-colors">Soluções</a></li>
                <li><a href="#projetos"
                        class="text-muted hover:text-emerald text-sm transition-colors">Projetos</a></li>
                <li><a href="#ecossistema"
                        class="text-muted hover:text-emerald text-sm transition-colors">Ecossistema</a></li>
                <li><a href="#sobre"
                        class="text-muted hover:text-emerald text-sm transition-colors">Nossos Números</a></li>
            </ul>
        </div>

        {{-- Institutional --}}
        <div class="text-center md:text-left">
            <h4 class="font-display text-white font-bold uppercase tracking-widest text-xs mb-6">Institucional</h4>
            <ul class="flex flex-col gap-3 md:items-start items-center">
                <li class="flex items-center justify-center md:justify-start gap-2 text-muted text-sm group/link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-muted/60 group-hover/link:text-emerald transition-colors"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    <a href="https://www.instagram.com/labsis.dev/" target="_blank" rel="noopener noreferrer" class="hover:text-emerald transition-colors">@labsis.dev</a>
                </li>
                <li class="flex items-center justify-center md:justify-start gap-2 text-muted text-sm mb-2 border-b border-white/5 pb-4 w-full md:w-auto">
                    <i data-lucide="map-pin" class="w-4 h-4 text-muted/60"></i> Araguaína - TO
                </li>
                <li class="flex flex-col items-center md:items-start gap-1 text-muted text-sm">
                    <span class="font-bold text-white/80">Responsável:</span>
                    <span>Prof. Msc Iury Gomes</span>
                    <span>Email: <a href="mailto:iury.oliveira@ifto.edu.br" class="hover:text-emerald transition-colors">iury.oliveira@ifto.edu.br</a></span>
                </li>
            </ul>
        </div>
    </div>

    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-xs text-muted/60">
            &copy; {{ now()->year }} LabSIS. Todos os direitos reservados.
        </p>
    </div>
</footer>
