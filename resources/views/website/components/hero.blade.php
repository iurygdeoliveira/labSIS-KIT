<section x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" class="w-full min-h-screen flex items-center">

    <div
        class="w-full max-w-screen-xl mx-auto px-6 lg:px-12 py-24 flex flex-col-reverse lg:flex-row items-center justify-between gap-12">

        {{-- Texto principal --}}
        <div class="text-center lg:text-left flex-1">
            <h1 x-show="show" x-transition:enter="transition ease-[cubic-bezier(0.23,1,0.32,1)] duration-1000"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="will-change-transform text-4xl lg:text-6xl font-extrabold leading-tight text-gray-900 dark:text-white">
                O sistema definitivo para ....
            </h1>

            <p x-show="show" x-transition:enter="transition ease-[cubic-bezier(0.23,1,0.32,1)] duration-1000 delay-200"
                x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
                class="will-change-transform mt-6 text-lg lg:text-xl text-gray-600 dark:text-gray-300">
                Organize ....., fidelize seus clientes e venda mais todos os dias.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <a x-show="show"
                    x-transition:enter="transition ease-[cubic-bezier(0.23,1,0.32,1)] duration-1000 delay-400"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    class="px-8 py-4 rounded-xl bg-teal-600 text-white font-semibold shadow hover:bg-teal-700 transition">
                    Comece agora
                </a>
                <a x-show="show"
                    x-transition:enter="transition ease-[cubic-bezier(0.23,1,0.32,1)] duration-1000 delay-500"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    class="px-8 py-4 rounded-xl border border-teal-600 text-teal-600 hover:bg-teal-600 hover:text-white transition">
                    Ver planos
                </a>
            </div>
        </div>

        {{-- Ilustração --}}
        <div class="flex-1 flex justify-center items-center">
            <img x-show="show"
                x-transition:enter="transition ease-[cubic-bezier(0.23,1,0.32,1)] duration-1000 delay-600"
                x-transition:enter-start="opacity-0 scale-90 translate-y-6"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                src="{{ asset('images/Capa.png') }}" alt="Ilustração"
                class="rounded-2xl shadow-lg w-full max-w-md h-auto object-contain dark:bg-white/5 bg-white">
        </div>
    </div>
</section>
