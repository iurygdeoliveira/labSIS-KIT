{{-- Hero Section --}}
<section class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-16 pb-16">

    {{-- Badge --}}
    <div data-aos="fade-up" data-aos-duration="1000">
        <a href="#"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-white text-sm font-bold mb-8 hover:bg-white/10 transition-all backdrop-blur-md cursor-pointer">
            <span class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose"></span>
            </span>
            IFTO Campus Araguaína
            <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
        </a>
    </div>

    {{-- Hero Logo --}}
    <div class="relative flex justify-center items-center mb-10" data-aos="zoom-out" data-aos-duration="1000"
        data-aos-delay="100">
        {{-- Ambient glow behind logo --}}
        <div
            class="absolute inset-0 bg-emerald/10 blur-[80px] rounded-full w-[250px] md:w-[400px] h-[80px] mx-auto pointer-events-none z-0">
        </div>
        <img src="{{ asset('images/labsis_logo_bg.png') }}" alt="LabSIS Logo Oficial"
            class="h-16 md:h-28 w-auto max-w-[min(100%,32rem)] relative z-10 opacity-90 drop-shadow-[0_0_15px_rgba(16,185,129,0.2)] hover:opacity-100 hover:drop-shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all duration-500 hover:-translate-y-1">
    </div>

    {{-- Headline --}}
    <h1 class="font-display text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-[1.1]"
        data-aos="zoom-y-out" data-aos-duration="1000" data-aos-delay="150">
        Laboratório de <br>
        <span class="text-gradient-emerald">Sistemas Inovadores</span>
    </h1>

    {{-- Subheadline --}}
    <p class="text-lg md:text-xl text-muted max-w-2xl mx-auto mb-12 leading-relaxed" data-aos="fade-up"
        data-aos-duration="1000" data-aos-delay="300">
        Transformamos desafios reais em soluções inteligentes. Criamos software que
        resolvem seus problemas.
    </p>

    {{-- CTAs --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4" data-aos="fade-up"
        data-aos-duration="1000" data-aos-delay="450">
        <a href="#diferenciais"
            class="w-full sm:w-auto px-8 py-3.5 bg-emerald hover:bg-emerald-hover text-[#080808] rounded-lg font-bold transition-all shadow-[0_0_30px_rgba(16,185,129,0.3)] hover:shadow-[0_0_50px_rgba(16,185,129,0.5)] transform hover:-translate-y-1 cursor-pointer flex items-center justify-center gap-2">
            Saiba Mais
            <i data-lucide="sparkles" class="w-4 h-4"></i>
        </a>
        <a href="https://www.instagram.com/labsis.dev/"
            class="w-full sm:w-auto px-8 py-3.5 bg-card border border-border hover:border-border-hover hover:bg-card/80 text-white rounded-lg font-bold text-base transition-all backdrop-blur-sm cursor-pointer flex items-center justify-center gap-2">
            Fale conosco!
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>

    {{-- Indicators --}}
    <div class="mt-14 text-sm text-muted flex flex-wrap items-center justify-center gap-x-6 gap-y-3 opacity-70"
        data-aos="fade-in" data-aos-duration="1000" data-aos-delay="600">
        <div class="flex items-center gap-2">
            <i data-lucide="check" class="w-4 h-4 text-emerald"></i>
            Experiência Real
        </div>
        <div class="flex items-center gap-2">
            <i data-lucide="check" class="w-4 h-4 text-emerald"></i>
            Mentoria de quem já fez
        </div>
        <div class="flex items-center gap-2">
            <i data-lucide="check" class="w-4 h-4 text-emerald"></i>
            Sistemas em Produção
        </div>
    </div>

</section>

{{-- Terminal Mockup --}}
<div class="w-full max-w-5xl mx-auto px-4 sm:px-6 relative z-20 -mt-10 mb-8" data-aos="fade-up"
    data-aos-duration="1200" data-aos-delay="200">
    <div
        class="relative rounded-xl border border-border bg-[#0a0a0a]/80 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
        {{-- Glow --}}
        <div class="absolute inset-0 bg-linear-to-t from-emerald/5 to-transparent opacity-50"></div>
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-border bg-[#111]">
            <div class="flex gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500/80 border border-red-500/50"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500/80 border border-yellow-500/50"></div>
                <div class="w-3 h-3 rounded-full bg-green-500/80 border border-green-500/50"></div>
            </div>
            <div class="text-xs text-muted font-mono flex items-center gap-2">
                <i data-lucide="cloud" class="w-3 h-3"></i> cloud-deploy
            </div>
            <div class="w-16"></div>
        </div>
        {{-- Content --}}
        <div class="p-6 md:p-8 font-mono text-[13px] md:text-sm leading-relaxed overflow-x-auto">
            <div class="text-muted mb-1"><span class="text-muted/50">00:00:06</span> Cloning application source
                control repository</div>
            <div class="text-muted mb-1"><span class="text-muted/50">00:00:06</span> Creating build environment
            </div>
            <div class="text-muted mb-1"><span class="text-muted/50">00:00:29</span> Running build commands
            </div>
            <div class="text-muted mb-1"><span class="text-muted/50">00:00:30</span> Uploading application</div>
            <div class="text-muted mb-4"><span class="text-muted/50">00:00:39</span> Preparing deploy
                environment</div>

            <div class="text-white mb-4">Deploying via Cloud deployment operator: <span
                    class="text-emerald">'v4.23.1'</span></div>

            <div class="flex items-center gap-3 mb-2 animate-pulse-glow" style="animation-delay: 0s;">
                <span class="text-emerald">&#10004;</span>
                <span class="text-white">Deployment queued</span>
                <span class="text-muted text-xs ml-auto">[513ms]</span>
            </div>
            <div class="flex items-center gap-3 mb-2 animate-pulse-glow" style="animation-delay: 0.2s;">
                <span class="text-emerald">&#10004;</span>
                <span class="text-white">Deployment received</span>
                <span class="text-muted text-xs ml-auto">[0s]</span>
            </div>
            <div class="flex items-center gap-3 mb-6 animate-pulse-glow" style="animation-delay: 0.4s;">
                <span class="text-emerald">&#10004;</span>
                <span class="text-white">Pulling application</span>
                <span class="text-muted text-xs ml-auto">[1&micro;s]</span>
            </div>

            <div class="inline-block px-3 py-1 bg-emerald-bg border border-emerald/30 text-emerald rounded">
                <span class="font-bold">&#10003; Deploy Completo.</span> Aplicação em produção.
            </div>
        </div>
        {{-- Animated bottom border --}}
        <div
            class="absolute bottom-0 left-0 w-full h-[2px] bg-linear-to-r from-transparent via-emerald to-transparent opacity-70">
        </div>
    </div>
</div>
