{{-- Ecossistema / Timeline --}}
<section id="ecossistema"
    class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative overflow-hidden z-10">
    <div class="flex flex-col lg:flex-row items-center gap-16 relative z-10">
        <div class="w-full lg:w-1/2" data-aos="fade-right" data-aos-duration="1000">
            <h2 class="font-display text-3xl md:text-5xl font-bold tracking-tight mb-6">
                Seu produto, <span class="text-gradient-emerald">perfeitamente
                    executado.</span>
            </h2>
            <p class="text-lg text-muted mb-10">
                Apoiamos empresas e pesquisadores na criação e evolução de projetos de software. Entregamos
                soluções precisas e confiáveis para impulsionar seus objetivos.
            </p>

            {{-- Steps --}}
            <div class="border-l border-border pl-6 space-y-8 relative">
                <div
                    class="absolute left-[-5px] top-2 w-2 h-2 rounded-full bg-emerald shadow-[0_0_10px_rgba(16,185,129,0.8)]">
                </div>
                <div
                    class="absolute left-0 top-10 bottom-0 w-px bg-linear-to-b from-emerald via-border to-transparent -ml-px">
                </div>

                <div class="group">
                    <h4
                        class="font-display text-white font-bold text-lg group-hover:text-emerald transition-colors cursor-default">
                        5. Entrega</h4>
                    <p class="text-muted text-sm mt-1">Deploy seguro em ambiente de produção escalável.</p>
                </div>
                <div class="group">
                    <h4
                        class="font-display text-white font-bold text-lg group-hover:text-rose transition-colors cursor-default">
                        4. Homologação</h4>
                    <p class="text-muted text-sm mt-1">Validação do cliente para garantia das especificações.
                    </p>
                </div>
                <div class="group">
                    <h4
                        class="font-display text-white font-bold text-lg group-hover:text-rose transition-colors cursor-default">
                        3. Qualidade e Testes</h4>
                    <p class="text-muted text-sm mt-1">Homologação contínua com cobertura rigorosa de testes.
                    </p>
                </div>
                <div class="group">
                    <h4
                        class="font-display text-white font-bold text-lg group-hover:text-emerald transition-colors cursor-default">
                        2. Desenvolvimento</h4>
                    <p class="text-muted text-sm mt-1">Implementação ágil orientada a valor e entregas
                        iterativas.</p>
                </div>
                <div class="group">
                    <h4
                        class="font-display text-white font-bold text-lg group-hover:text-emerald transition-colors cursor-default">
                        1. Planejamento</h4>
                    <p class="text-muted text-sm mt-1">Levantamento de requisitos, regras de negócio e
                        arquitetura.</p>
                </div>
            </div>
        </div>

        {{-- Diagrama de nodes --}}
        <div class="w-full lg:w-1/2" data-aos="fade-left" data-aos-duration="1000">
            <div
                class="relative rounded-2xl border border-border bg-card p-2 md:p-8 shadow-2xl overflow-hidden group">
                <div
                    class="absolute inset-0 bg-linear-to-br from-emerald/10 via-transparent to-transparent opacity-50 group-hover:opacity-100 transition-opacity duration-700">
                </div>

                <div class="mb-4 relative z-10 p-4 pb-0 text-center">
                    <h3 class="font-display font-bold text-xl text-white">Nosso Ecossistema Moderno</h3>
                    <p class="text-sm text-muted mt-1">Stack tecnológica utilizada no LabSIS</p>
                </div>

                <div class="flex flex-col gap-3 relative z-10 p-4 pt-0">
                    {{-- Node 1: Laravel --}}
                    <div
                        class="flex items-center gap-4 p-3.5 rounded-xl bg-base border border-border shadow-lg transition-transform duration-500 hover:scale-105">
                        <div
                            class="w-10 h-10 rounded-lg bg-rose/10 flex items-center justify-center text-rose ring-1 ring-rose/30">
                            <i data-lucide="blocks" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-white text-sm tracking-wide">Backend & Core
                            </h4>
                            <span class="text-xs text-muted">Arquitetura robusta com <strong
                                    class="text-white/80 font-semibold">Laravel</strong> e PHP</span>
                        </div>
                    </div>

                    <div class="h-3 w-px bg-linear-to-b from-rose/40 to-emerald/20 mx-8"></div>

                    {{-- Node 2: Livewire / Filament --}}
                    <div
                        class="flex items-center gap-4 p-3.5 rounded-xl bg-emerald/5 border border-emerald/40 shadow-[0_0_20px_rgba(16,185,129,0.15)] ring-1 ring-emerald/30 transition-transform duration-500 hover:scale-105 relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald"></div>
                        <div
                            class="w-10 h-10 rounded-lg bg-emerald/20 flex items-center justify-center text-emerald">
                            <i data-lucide="layout-template" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4
                                class="font-display font-bold text-white text-sm tracking-wide flex items-center gap-2">
                                Frontend & Painéis Dinâmicos <span
                                    class="flex h-2 w-2 rounded-full bg-emerald animate-pulse"></span></h4>
                            <span class="text-xs text-emerald/80">Reatividade com <strong
                                    class="font-semibold text-emerald">Livewire</strong> & <strong
                                    class="font-semibold text-emerald">Filament</strong></span>
                        </div>
                    </div>

                    <div class="h-3 w-px bg-linear-to-b from-emerald/20 to-rose/20 mx-8"></div>

                    {{-- Node 3: PostgreSQL --}}
                    <div
                        class="flex items-center gap-4 p-3.5 rounded-xl bg-base border border-border shadow-lg transition-transform duration-500 hover:scale-105">
                        <div
                            class="w-10 h-10 rounded-lg bg-rose/10 flex items-center justify-center text-rose ring-1 ring-rose/30">
                            <i data-lucide="database" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-white text-sm tracking-wide">Bancos de Dados
                            </h4>
                            <span class="text-xs text-muted">Escalabilidade e controle com <strong
                                    class="text-white/80 font-semibold">PostgreSQL</strong></span>
                        </div>
                    </div>

                    <div class="h-3 w-px bg-linear-to-b from-rose/20 to-emerald/20 mx-8"></div>

                    {{-- Node 4: Mobile --}}
                    <div
                        class="flex items-center gap-4 p-3.5 rounded-xl bg-base border border-border shadow-lg transition-transform duration-500 hover:scale-105">
                        <div
                            class="w-10 h-10 rounded-lg bg-emerald/10 flex items-center justify-center text-emerald ring-1 ring-emerald/30">
                            <i data-lucide="smartphone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-white text-sm tracking-wide">Experiência
                                Mobile</h4>
                            <span class="text-xs text-muted">Aplicativos ágeis via <strong
                                    class="text-white/80 font-semibold">NativePHP</strong></span>
                        </div>
                    </div>

                    <div class="h-3 w-px bg-linear-to-b from-emerald/20 to-rose/40 mx-8"></div>

                    {{-- Node 5: Cloud --}}
                    <div
                        class="flex items-center gap-4 p-3.5 rounded-xl bg-base border border-border shadow-lg transition-transform duration-500 hover:scale-105">
                        <div
                            class="w-10 h-10 rounded-lg bg-rose/10 flex items-center justify-center text-rose ring-1 ring-rose/30">
                            <i data-lucide="cloud" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-white text-sm tracking-wide">Infraestrutura
                            </h4>
                            <span class="text-xs text-muted">Deploy seguro em ambiente <strong
                                    class="text-white/80 font-semibold">Cloud</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
