<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'LabSIS - Laboratório de Sistemas Inovadores') }}</title>

    {{-- SEO Meta Tags --}}
    <meta name="description"
        content="LabSIS é o laboratório de sistemas inovadores do IFTO Araguaína, focado em transformar desafios reais em soluções de software inteligentes.">
    <meta name="keywords"
        content="LabSIS, IFTO, Araguaína, Laboratório de Software, Inovação, Tecnologia, Ensino, Projetos Reais, Mentoria">
    <meta name="author" content="LabSIS">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ config('app.name', 'LabSIS') }}">
    <meta property="og:description"
        content="LabSIS é o laboratório de sistemas inovadores do IFTO Araguaína, focado em transformar desafios reais em soluções de software inteligentes.">
    <meta property="og:image" content="{{ asset('images/labsis_logo_bg.png') }}">
    <meta property="og:site_name" content="LabSIS">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- AOS Animate On Scroll (Local) --}}
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet">

    {{-- Lucide Icons (Local) --}}
    <script src="{{ asset('vendor/lucide/lucide.js') }}"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="relative min-h-screen flex flex-col antialiased selection:bg-emerald/30 selection:text-white bg-[#080808] text-white overflow-x-hidden">

    {{-- Ambient Glowing Orbs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="color-orb w-[600px] h-[600px] bg-emerald/20 -top-[200px] -left-[100px] animate-blob"></div>
        <div class="color-orb w-[500px] h-[500px] bg-rose/15 top-[20%] -right-[100px] animate-blob-reverse" style="animation-delay: 2s;"></div>
        <div class="color-orb w-[700px] h-[700px] bg-emerald/10 bottom-[-200px] left-[20%] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    {{ $slot }}

    @livewire('notifications')

    @filamentScripts
    @vite('resources/js/app.js')

    {{-- AOS Initialization (Local) --}}
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                once: true,
                offset: 50,
                easing: 'ease-out-cubic',
            });
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        document.addEventListener('livewire:navigated', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
