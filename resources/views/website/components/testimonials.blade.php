@php
    $testimonials = [
        [
            'name' => 'Carla Menezes',
            'role' => 'Esteticista',
            'quote' => 'Facilitou muito meu dia a dia com os clientes. Agendar ficou simples e profissional!',
        ],
        [
            'name' => 'Eduardo Lima',
            'role' => 'Barbearia Elite',
            'quote' => 'A plataforma nos ajudou a organizar e crescer. Os relatórios são um diferencial.',
        ],
        [
            'name' => 'Juliana Rocha',
            'role' => 'Fisioterapeuta',
            'quote' => 'Eu finalmente parei de esquecer horários! E meus pacientes adoram os lembretes.',
        ],
        [
            'name' => 'Lucas Silva',
            'role' => 'Personal Trainer',
            'quote' => 'Uso todos os dias. O suporte é excelente e o sistema é muito estável.',
        ],
        [
            'name' => 'Ana Paula',
            'role' => 'Consultora de Beleza',
            'quote' => 'Adoro a facilidade de configurar horários e a integração com o WhatsApp.',
        ],
        [
            'name' => 'Roberto Dias',
            'role' => 'Clínica Vida',
            'quote' => 'Organizamos tudo em um só lugar. Não abrimos mão da plataforma.',
        ],
    ];
@endphp

<section x-data x-init="window.startTestimonialScroll($refs.track)" class="w-full py-24 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="max-w-screen-xl mx-auto px-6 lg:px-12 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold mb-12">O que dizem nossos clientes?</h2>

        <div class="relative w-full overflow-x-hidden">
            <div x-ref="track" class="no-scrollbar flex gap-6"
                style="overflow-x: scroll; display: flex; flex-wrap: nowrap; width: 100%;">
                @foreach (array_merge($testimonials, $testimonials) as $testimonial)
                    <div
                        class="min-w-[300px] max-w-[300px] bg-teal-50 dark:bg-[#18191a] rounded-xl p-6 shadow flex-shrink-0">
                        <div class="mb-4">
                            <div
                                class="w-16 h-16 mx-auto rounded-full bg-teal-200 dark:bg-teal-700 flex items-center justify-center text-white text-xl font-bold">
                                {{ substr($testimonial['name'], 0, 1) }}
                            </div>
                        </div>
                        <blockquote class="italic text-gray-700 dark:text-gray-300 mb-4">“{{ $testimonial['quote'] }}”
                        </blockquote>
                        <p class="font-semibold">{{ $testimonial['name'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $testimonial['role'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        window.startTestimonialScroll = function(el) {
            if (!el) return;

            const scrollSpeed = 0.5;
            let isPaused = false;

            // Pausar ao passar o mouse
            el.addEventListener('mouseenter', () => isPaused = true);
            el.addEventListener('mouseleave', () => isPaused = false);

            const animate = () => {
                if (!isPaused) {
                    el.scrollLeft += scrollSpeed;

                    if (el.scrollLeft >= el.scrollWidth - el.clientWidth) {
                        el.scrollLeft = 0;
                    }
                }

                requestAnimationFrame(animate);
            };

            requestAnimationFrame(animate);
        };
    </script>
    </script>
@endpush
