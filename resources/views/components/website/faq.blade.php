<section class="py-24 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-center mb-12">Perguntas Frequentes</h1>

        <div x-data="{ openItem: null }" class="space-y-4">
            @foreach ([
        ['question' => 'Qual é a política de reembolso?', 'answer' => 'Se você não estiver satisfeito com a sua compra, oferecemos garantia de reembolso de 30 dias. Entre em contato com nosso suporte.'],
        ['question' => 'Oferecem desconto para compras em grande volume?', 'answer' => 'Sim! Entre em contato com nosso time comercial para planos personalizados.'],
        ['question' => 'Como acompanho meu pedido?', 'answer' => 'Após o envio, você receberá um e-mail com o código de rastreamento.'],
        ['question' => 'É possível mudar de plano depois da contratação?', 'answer' => 'Sim, você pode alterar o plano a qualquer momento através do seu painel.'],
        ['question' => 'Os planos possuem fidelidade?', 'answer' => 'Não. Todos os planos são sem fidelidade e você pode cancelar quando quiser.'],
        ['question' => 'Posso testar gratuitamente?', 'answer' => 'Sim, oferecemos um período de teste gratuito de 7 dias.'],
        ['question' => 'É possível integrar com outros sistemas?', 'answer' => 'Sim, oferecemos integração via API e suporte para ferramentas populares.'],
        ['question' => 'Qual o horário de atendimento do suporte?', 'answer' => 'Nosso suporte funciona de segunda a sexta, das 9h às 18h (horário de Brasília).'],
    ] as $index => $faq)
                <div x-data="{ id: {{ $index }} }"
                    class="border border-teal-200 dark:border-teal-700 rounded-lg overflow-hidden">
                    <button @click="openItem === id ? openItem = null : openItem = id"
                        class="w-full text-left px-6 py-4 bg-teal-50 dark:bg-[#18191a] hover:bg-teal-100 dark:hover:bg-[#252227] transition flex items-center justify-between">
                        <span class="font-semibold text-lg">{{ $faq['question'] }}</span>
                        <svg x-show="openItem !== id" class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                        <svg x-show="openItem === id" x-cloak class="w-5 h-5 text-teal-600" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 12H4" />
                        </svg>
                    </button>
                    <div x-show="openItem === id" x-transition x-collapse
                        class="px-6 py-4 text-gray-700 dark:text-gray-300 bg-white dark:bg-[#18191a]">
                        {{ $faq['answer'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
