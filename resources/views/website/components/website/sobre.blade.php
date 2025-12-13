{{-- Sobre o Projeto Section --}}
<section id="sobre" class="py-16 mx-4 bg-white rounded-3xl">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- Header da Seção --}}
        <div class="mb-16 text-center">
            <div class="flex items-center justify-center mb-8">
                <h2 class="ml-4 text-3xl font-bold tracking-tight text-emerald-900 sm:text-4xl">
                    Sobre o LabSIS
                </h2>
            </div>
            
            {{-- Introdução --}}
            <div class="max-w-4xl mx-auto space-y-6 text-lg text-gray-700">
                <p class="text-xl text-justify">
                    O LabSIS não é apenas um laboratório. Visa se tornar o <strong class="text-emerald-900">epicentro da inovação em Araguaína - TO</strong>, um ecossistema dinâmico onde estudantes, professores e empresas se conectam para um propósito único: <strong class="text-emerald-900">transformar desafios reais em software de impacto real.</strong> Nosso objetivo é criar um ambiente onde você, estudante, vai além da teoria e mergulha em projetos reais de tecnologia. Aqui, você não apenas aprende — você constrói, lidera e entrega soluções que funcionam no mundo real, enquanto transforma seu currículo em um portfólio de impacto.
                </p>
            </div>
        </div>

        {{-- Benefícios para o Discente --}}
        <div class="flex justify-center">
            <div class="grid gap-8 lg:grid-cols-2 max-w-5xl">
            {{-- Experiência Real --}}
            <div class="p-6 border bg-linear-to-br from-emerald-50 to-emerald-100 rounded-2xl border-emerald-200">
                <div class="mb-4 text-center">
                    <h3 class="text-xl font-bold text-emerald-900">Experiência Real</h3>
                </div>
                <p class="text-justify text-gray-700">
                    Chega de projetos fictícios. No LabSIS, você trabalha em desafios reais de empresas e organizações. Cada linha de código que você escreve aqui tem um propósito: resolver problemas concretos e gerar impacto mensurável. <strong class="text-emerald-900">Quando se formar, você não terá apenas um diploma — terá um histórico comprovado de entregas.</strong>
                </p>
            </div>

            {{-- Mentoria --}}
            <div class="p-6 border border-emerald-200 bg-linear-to-br from-emerald-50 to-emerald-100 rounded-2xl">
                <div class="mb-4 text-center">
                    <h3 class="text-xl font-bold text-emerald-900">Mentoria de quem já fez</h3>
                </div>
                <p class="text-justify text-gray-700">
                    Aprenda com professores pesquisadores e profissionais do mercado que vivem tecnologia todos os dias. Tenha acesso a orientação técnica de alto nível, feedback constante e networking com quem pode abrir portas na sua carreira. <strong class="text-emerald-900">Aqui, você não está sozinho — está cercado por quem quer ver você vencer.</strong>
                </p>
            </div>

            </div>
        </div>

        {{-- Imagem Ilustrativa --}}
        <div class="mt-16">
            <div class="relative w-full max-w-4xl mx-auto overflow-hidden shadow-2xl rounded-2xl">
                <img src="{{ asset('images/jovens.png') }}" alt="Jovens profissionais trabalhando em tecnologia" class="object-cover w-full h-auto min-h-96">
            </div>
        </div>
    </div>
</section> 