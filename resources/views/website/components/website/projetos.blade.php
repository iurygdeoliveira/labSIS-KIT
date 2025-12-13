{{-- Projetos Desenvolvidos Section --}}
<section id="projetos" class="py-10 mx-4 my-4 bg-gray-100 rounded-3xl">
    @php
        // Projetos fictícios para demonstração
        $projects = [
            (object) [
                'name' => 'LembreMED',
                'description' => 'Sistema inteligente de gerenciamento de medicamentos que auxilia pacientes e cuidadores no controle de tratamentos médicos, com lembretes personalizados e histórico completo.',
                'status' => null,
                'software_registration' => 'BR512024000123-4',
                'cover' => null,
            ],
            (object) [
                'name' => 'LabSIS Manager',
                'description' => 'Plataforma de gestão de projetos acadêmicos desenvolvida para facilitar o acompanhamento e organização de trabalhos de conclusão de curso e projetos de pesquisa.',
                'status' => null,
                'software_registration' => 'BR512023000456-7',
                'cover' => null,
            ],
            (object) [
                'name' => 'EduTrack',
                'description' => 'Sistema de acompanhamento pedagógico que permite professores monitorarem o progresso individual dos alunos através de dashboards interativos e relatórios detalhados.',
                'status' => null,
                'software_registration' => null,
                'cover' => null,
            ],
        ];
        $totalProjects = count($projects);
    @endphp

        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-emerald-900 sm:text-4xl">
            Projetos
            </h2>
        </div>
        
    <div
            x-data="{
                current: 0,
                auto: null,
                itemWidth() {
                    const track = this.$refs.track;
                    if (! track) { return 0; }
                    const first = track.querySelector('[data-item]');
                    return first ? first.getBoundingClientRect().width + 32 : 0; // 32 = gap-8
                },
                scrollTo(index) {
                    const track = this.$refs.track;
                    if (! track) { return; }
                    const w = this.itemWidth();
                    track.scrollTo({ left: index * w, behavior: 'smooth' });
                    this.current = index;
                },
                next() {
                    const track = this.$refs.track;
                    if (! track) { return; }
                    const maxIndex = Math.max(0, track.querySelectorAll('[data-item]').length - 1);
                    this.scrollTo(this.current >= maxIndex ? 0 : this.current + 1);
                },
                startAuto() {
                    this.auto && clearInterval(this.auto);
                    this.auto = setInterval(() => this.next(), 3000);
                },
            }"
            x-init="startAuto()"
            class="relative mt-10"
        >
            <div
                x-ref="track"
                class="flex gap-8 pb-4 overflow-x-auto snap-x snap-mandatory scroll-smooth"
                style="-ms-overflow-style: none; scrollbar-width: none;"
            >
                <style>
                    #projetos [x-ref=track]::-webkit-scrollbar { display: none; }
                </style>

                @forelse($projects as $project)
                    <div data-item class="w-full snap-center shrink-0 md:w-1/2 lg:w-1/3">
                        <div class="relative overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl group hover:shadow-xl hover:-translate-y-1 flex flex-col h-full">
                            {{-- Ícone do Projeto --}}
                            <div class="relative w-full h-28 shrink-0 bg-linear-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-2 bg-white/20 backdrop-blur-sm rounded-full">
                                        <span class="text-3xl font-bold text-white">{{ mb_substr($project->name ?? 'P', 0, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Conteúdo do Card --}}
                            <div class="p-6 flex flex-col grow">
                                <h3 class="text-xl font-semibold text-emerald-900 text-center mb-4">{{ $project->name ?? 'Projeto' }}</h3>
                                
                                @if(! empty($project->description))
                                    <p class="mt-2 text-gray-600 text-justify grow">{{ $project->description }}</p>
                                @else
                                    <p class="mt-2 text-gray-600 text-justify grow">Projeto do LabSIS</p>
                                @endif
                                
                                <div class="flex flex-wrap gap-2 mt-4 justify-center">
                                    {{-- Registro de Software --}}
                                    @if($project->software_registration)
                                        <span class="px-3 py-1 text-xs font-medium bg-emerald-100 rounded-full text-emerald-900">
                                            📋 {{ $project->software_registration }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-full">
                        <p class="text-center text-gray-600">Nenhum projeto cadastrado ainda.</p>
                    </div>
                @endforelse
            </div>
            
        @if ($totalProjects > 3)
            <div class="mt-2 text-center">
                <a href="#" class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white rounded-lg bg-emerald-900 hover:bg-emerald-800">
                    Ver todos
                </a>
            </div>
        @endif
    </div>
</section> 