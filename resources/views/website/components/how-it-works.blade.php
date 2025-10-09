<section class="w-full py-24 text-gray-900 dark:text-white">
    <div class="max-w-screen-xl mx-auto px-6 lg:px-12 text-center relative">
        <h2 class="text-3xl lg:text-4xl font-bold mb-16 relative z-10">
            Como funciona na prática?
        </h2>

        {{-- Linha horizontal atrás --}}
        <div
            class="hidden md:block absolute left-0 right-0 top-1/2 transform -translate-y-1/2 h-0.5 bg-teal-300 dark:bg-teal-700 z-0">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8 items-start relative z-10">
            @foreach ([['icon' => 'heroicon-o-user-plus', 'title' => 'Crie sua conta', 'desc' => 'Comece seu cadastro em poucos segundos.'], ['icon' => 'heroicon-o-currency-dollar', 'title' => 'Escolha o plano', 'desc' => 'Selecione o que melhor se adapta ao seu negócio.'], ['icon' => 'heroicon-o-cog-6-tooth', 'title' => 'Configure o sistema', 'desc' => 'Personalize suas preferências e horários.'], ['icon' => 'heroicon-o-calendar-days', 'title' => 'Agende e atenda', 'desc' => 'Comece a usar e atender seus clientes.']] as $index => $step)
                <div x-data="{ show: false }"
                    x-intersect.once="$nextTick(() => setTimeout(() => show = true, {{ $index * 250 }}))"
                    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="transition duration-[1200ms] ease-[cubic-bezier(0.23,1,0.32,1)] flex flex-col items-center text-center px-4">
                    <div class="mb-4 bg-teal-100 dark:bg-teal-800 rounded-full p-4 z-10 relative">
                        {!! svg($step['icon'])->class('w-8 h-8 text-teal-700 dark:text-white')->toHtml() !!}
                    </div>
                    <h3 class="text-xl font-semibold mb-2">
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $step['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
