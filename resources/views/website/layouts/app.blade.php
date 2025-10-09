<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('website.partials.head.head')

    @vite(['resources/css/app.css'])
    @livewireStyles

    @stack('styles')
</head>

<body
    class="min-h-screen font-sans antialiased text-gray-900 transition-colors duration-700 bg-white dark:bg-gray-900 dark:text-white">
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

    <footer>
        @include('website.partials.footer.footer')
    </footer>
</body>

</html>
