<div x-data="{
    currentCycle: 'MONTHLY',
    formatBRL(value) {
        return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    animateNumber(el, from, to, duration = 600) {
        const start = performance.now();
        const easeOut = t => t * (2 - t);
        const step = now => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = easeOut(progress);
            const value = from + (to - from) * eased;
            el.textContent = this.formatBRL(value);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    },
    switchCycle(cycle) {
        this.currentCycle = cycle;
        document.querySelectorAll('.plan-card').forEach(card => {
            const block = card.querySelector('.price-block');
            const amount = card.querySelector('.price-amount');
            const label = card.querySelector('.cycle-label');

            const monthly = parseFloat(block.dataset.monthly || 0);
            const yearly = parseFloat(block.dataset.yearly || 0);
            const oldValue = parseFloat(amount.textContent.replace('.', '').replace(',', '.')) || 0;
            const newValue = cycle === 'MONTHLY' ? monthly : yearly;

            this.animateNumber(amount, oldValue, newValue);
            label.textContent = cycle === 'MONTHLY' ? '/mês' : '/ano';
        });
    }
}" x-init="switchCycle(currentCycle)" class="max-w-6xl mx-auto text-center">
    <h2 class="mb-4 text-4xl font-bold text-teal-600 dark:text-teal-400">Escolha seu plano</h2>
    <p class="mb-10 text-gray-600 dark:text-gray-300">
        Encontre o plano ideal para sua empresa.
    </p>

    <!-- Toggle -->
    <div class="flex justify-center mb-12">
        <div class="inline-flex p-1 bg-gray-200 rounded-full dark:bg-[#18191a]">
            <button @click="switchCycle('MONTHLY')"
                :class="currentCycle === 'MONTHLY' ? 'bg-white dark:bg-[#252227]' : ''"
                class="px-6 py-2 text-sm font-semibold text-gray-800 transition rounded-full dark:text-white hover:bg-teal-50 dark:hover:bg-[#252227]">
                Mensal
            </button>
            <button @click="switchCycle('YEARLY')"
                :class="currentCycle === 'YEARLY' ? 'bg-white dark:bg-[#252227]' : ''"
                class="px-6 py-2 text-sm font-semibold text-gray-800 transition rounded-full dark:text-white hover:bg-teal-50 dark:hover:bg-[#252227]">
                Anual
            </button>
        </div>
    </div>

    <!-- Plan Cards -->
    <div class="flex flex-wrap justify-center gap-6 md:gap-3">
        @foreach ([
        [
            'name' => 'Plano Essencial',
            'description' => 'Ideal para começar',
            'monthly' => 49.9,
            'yearly' => 499.0,
            'features' => ['Suporte Básico', '1 Usuário', 'Relatórios Simples'],
        ],
        [
            'name' => 'Plano Profissional',
            'description' => 'Para equipes em crescimento',
            'monthly' => 89.9,
            'yearly' => 899.0,
            'features' => ['Suporte Prioritário', 'Até 5 Usuários', 'Relatórios Avançados', 'Dashboard Interativo', 'Acesso via API', 'Exportação em Excel'],
        ],
        [
            'name' => 'Plano Corporativo',
            'description' => 'Para grandes empresas',
            'monthly' => 199.9,
            'yearly' => 1990.0,
            'features' => ['Suporte Dedicado', 'Usuários Ilimitados', 'Integrações Personalizadas', 'Ambiente de Testes (sandbox)', 'Acompanhamento Técnico', 'SLA de Atendimento', 'Single Sign-On (SSO)', 'Relatórios Personalizados'],
        ],
    ] as $plan)
            <div
                class="plan-card w-full sm:w-[20rem] md:w-[22rem] border border-gray-300 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-[#18191a] flex flex-col transition hover:shadow-2xl hover:-translate-y-1 hover:border-teal-500 dark:hover:border-teal-400">
                <h3 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $plan['name'] }}</h3>
                <p class="mb-4 text-gray-600 dark:text-gray-400">{{ $plan['description'] }}</p>

                <!-- Preço -->
                <div class="flex items-baseline justify-center mb-4 price-block" data-monthly="{{ $plan['monthly'] }}"
                    data-yearly="{{ $plan['yearly'] }}">
                    <span class="mr-1 text-xl font-semibold text-gray-800 dark:text-gray-300">R$</span>
                    <span
                        class="text-3xl font-bold text-teal-600 transition-all duration-300 dark:text-teal-400 price-amount">
                        {{ number_format($plan['monthly'], 2, ',', '.') }}
                    </span>
                    <span class="ml-1 text-base font-normal text-gray-500 dark:text-gray-400 cycle-label">
                        /mês
                    </span>
                </div>

                <ul class="mb-6 space-y-2 text-left text-gray-700 dark:text-gray-300">
                    @foreach ($plan['features'] as $feature)
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 111.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>

                <a href="#"
                    class="block w-full px-4 py-2 mt-auto text-white transition bg-teal-600 rounded-md hover:bg-teal-700 dark:hover:bg-teal-500">
                    Assinar
                </a>
            </div>
        @endforeach
    </div>
</div>
