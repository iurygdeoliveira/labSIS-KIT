<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('website.partials.head.head')

    @vite(['resources/css/app.css'])
    @fluxAppearance
    @livewireStyles

    @stack('styles')
    
    {{-- Força modo light --}}
    <script>
        document.documentElement.classList.remove('dark');
        localStorage.removeItem('dark-mode');
    </script>
</head>

<body
    class="min-h-screen font-sans antialiased text-gray-900 transition-colors duration-700 bg-white">
    {{-- Header --}}
    <header>
        @include('website.partials.header.header')
    </header>

    {{-- Conteúdo principal --}}
    <main class="pt-20 pb-10">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
    @stack('modals')
    @livewireScripts
    @fluxScripts

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Scroll Offset Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para ajustar o scroll com offset
            function smoothScrollTo(element, offset = 80) {
                const elementPosition = element.offsetTop - offset;
                window.scrollTo({
                    top: elementPosition,
                    behavior: 'smooth'
                });
            }

            // Intercepta cliques em links com hash
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href*="#"]');
                if (link) {
                    const href = link.getAttribute('href');
                    const hash = href.split('#')[1];
                    
                    if (hash) {
                        e.preventDefault();
                        const targetElement = document.getElementById(hash);
                        
                        if (targetElement) {
                            smoothScrollTo(targetElement, 80); // 80px de offset para o header
                        }
                    }
                }
            });

            // Se a URL já tem hash, ajusta o scroll na carga da página
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const targetElement = document.getElementById(hash);
                
                if (targetElement) {
                    setTimeout(() => {
                        smoothScrollTo(targetElement, 80);
                    }, 100);
                }
            }
        });
    </script>

    <footer>
        @include('website.partials.footer.footer')
    </footer>
</body>

</html>
