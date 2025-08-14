@php
    $benefits = [
        [
            'icon' => 'heroicon-o-clock',
            'title' => 'Agendamento rápido',
            'desc' => 'Organize horários com poucos cliques.',
        ],
        [
            'icon' => 'heroicon-o-chart-bar',
            'title' => 'Métricas em tempo real',
            'desc' => 'Acompanhe o desempenho da sua empresa.',
        ],
        [
            'icon' => 'heroicon-o-chat-bubble-left-right',
            'title' => 'Integração WhatsApp',
            'desc' => 'Atenda clientes direto pelo app.',
        ],
        [
            'icon' => 'heroicon-o-user-group',
            'title' => 'Gestão de equipe',
            'desc' => 'Distribua atendimentos com facilidade.',
        ],
        [
            'icon' => 'heroicon-o-shield-check',
            'title' => 'Segurança de dados',
            'desc' => 'Informações protegidas e criptografadas.',
        ],
        [
            'icon' => 'heroicon-o-device-phone-mobile',
            'title' => 'Totalmente responsivo',
            'desc' => 'Acesse de qualquer dispositivo.',
        ],
    ];
@endphp

<section class="w-full py-24 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="max-w-screen-xl mx-auto px-6 lg:px-12 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold mb-12">
            Por que escolher nossa plataforma?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
            @foreach ($benefits as $index => $benefit)
                <div x-data="{ show: false }"
                    x-intersect.once="$nextTick(() => setTimeout(() => show = true, {{ $index * 200 }}))"
                    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-500 ease-out transform bg-teal-50 dark:bg-[#18191a] rounded-xl p-6 shadow-sm hover:shadow-xl hover:scale-105 flex flex-col min-h-[240px]">
                    <div class="mb-4">
                       {!! svg($benefit['icon'])->class('w-10 h-10 text-teal-600 mx-auto')->toHtml() !!}

                    </div>

                    <div class="flex-1 flex flex-col justify-between text-center">
                        <h3 class="text-xl font-semibold mb-2">
                            {{ $benefit['title'] }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $benefit['desc'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
