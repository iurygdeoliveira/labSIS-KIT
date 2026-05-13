<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LabSIS - Laboratório de Sistemas Inovadores')</title>

    {{-- SEO Meta Tags --}}
    <meta name="description"
        content="@yield('description', 'LabSIS é o laboratório de sistemas inovadores do IFTO Araguaína, focado em transformar desafios reais em soluções de software inteligentes.')">
    <meta name="keywords"
        content="LabSIS, IFTO, Araguaína, Laboratório de Software, Inovação, Tecnologia, Ensino, Projetos Reais, Mentoria">
    <meta name="author" content="LabSIS">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'LabSIS - Laboratório de Sistemas Inovadores')">
    <meta property="og:description"
        content="@yield('description', 'LabSIS é o laboratório de sistemas inovadores do IFTO Araguaína, focado em transformar desafios reais em soluções de software inteligentes.')">
    <meta property="og:image" content="{{ asset('images/labsis_logo_bg.png') }}">
    <meta property="og:site_name" content="LabSIS">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'LabSIS - Laboratório de Sistemas Inovadores')">
    <meta name="twitter:description"
        content="@yield('description', 'LabSIS é o laboratório de sistemas inovadores do IFTO Araguaína, focado em transformar desafios reais em soluções de software inteligentes.')">
    <meta name="twitter:image" content="{{ asset('images/labsis_logo_bg.png') }}">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- AOS Animate On Scroll (Local) --}}
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet">

    {{-- Lucide Icons (Local) --}}
    <script src="{{ asset('vendor/lucide/lucide.js') }}"></script>

    {{-- Tailwind CSS v4 Browser Build (Local) --}}
    <script src="{{ asset('vendor/tailwindcss/tailwindcss-browser.js') }}"></script>

    <style type="text/tailwindcss">
        @theme {
            --font-display: 'Plus Jakarta Sans', sans-serif;
            --font-body: 'Inter', sans-serif;

            --color-base: #080808;
            --color-card: #121212;
            --color-border: rgba(255, 255, 255, 0.08);
            --color-border-hover: rgba(255, 255, 255, 0.2);
            --color-muted: #8a8f98;
            --color-emerald: #10b981;
            --color-emerald-hover: #34d399;
            --color-emerald-bg: rgba(16, 185, 129, 0.1);
            --color-rose: #e11d48;
            --color-rose-hover: #fb7185;

            --shadow-glow-emerald: 0 0 40px -10px rgba(16, 185, 129, 0.4);
            --shadow-glow-rose: 0 0 40px -10px rgba(225, 29, 72, 0.4);
        }

        body {
            background-color: var(--color-base);
            color: #ffffff;
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: var(--font-display);
            letter-spacing: -0.02em;
        }

        /* Grid background */
        .bg-grid {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* Glass header */
        .glass-header {
            background: rgba(8, 8, 8, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Animated border cards */
        .animated-border-card {
            position: relative;
            background: var(--color-card);
            border-radius: 0.75rem;
            z-index: 1;
        }
        .animated-border-card::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: 0.85rem;
            background: conic-gradient(from 0deg, transparent 0 340deg, rgba(255,255,255,0.3) 360deg);
            z-index: -1;
            animation: spin 3s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .animated-border-card:hover::before {
            opacity: 1;
        }
        .animated-border-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--color-card);
            border-radius: 0.75rem;
            z-index: -1;
        }
        .animated-border-card.glow-emerald:hover::before {
            background: conic-gradient(from 0deg, transparent 0 340deg, rgba(16,185,129,0.5) 360deg);
        }
        .animated-border-card.glow-rose:hover::before {
            background: conic-gradient(from 0deg, transparent 0 340deg, rgba(225,29,72,0.5) 360deg);
        }

        /* Spinning border for CTA */
        .spinning-border {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
        }
        .spinning-border::before {
            content: "";
            position: absolute;
            inset: -200%;
            background: conic-gradient(from 90deg at 50% 50%, #10b981 0%, transparent 50%, #10b981 100%);
            animation: spin 2s linear infinite;
            opacity: 0.6;
        }
        .spinning-border > span {
            position: relative;
            display: inline-flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            border-radius: 0.65rem;
            background: var(--color-base);
        }

        /* Color orbs */
        .color-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.4;
        }

        /* Text gradients */
        .text-gradient {
            background: linear-gradient(90deg, #fff 0%, #a3a3a3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-emerald {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animations */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes blob-reverse {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(-30px, 50px) scale(1.2); }
            66% { transform: translate(20px, -20px) scale(0.8); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-blob { animation: blob 10s infinite; }
        .animate-blob-reverse { animation: blob-reverse 12s infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        /* Hide scrollbar for carousel */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</head>

<body class="relative min-h-screen flex flex-col antialiased selection:bg-emerald/30 selection:text-white">

    {{-- Ambient Glowing Orbs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="color-orb w-[600px] h-[600px] bg-emerald/20 -top-[200px] -left-[100px] animate-blob"></div>
        <div class="color-orb w-[500px] h-[500px] bg-rose/15 top-[20%] -right-[100px] animate-blob-reverse"
            style="animation-delay: 2s;"></div>
        <div class="color-orb w-[700px] h-[700px] bg-emerald/10 bottom-[-200px] left-[20%] animate-blob"
            style="animation-delay: 4s;"></div>
    </div>

    {{-- Header --}}
    @include('website.components.website-v2.header')

    {{-- Main Content --}}
    <main class="flex-grow pt-32 relative w-full flex flex-col items-center">
        {{-- Hero Background Grid --}}
        <div class="bg-grid"></div>

        @yield('content')
    </main>

    {{-- Footer --}}
    @include('website.components.website-v2.footer')

    {{-- AOS Initialization (Local) --}}
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
            easing: 'ease-out-cubic',
        });
    </script>

    {{-- Lucide Icons Initialization --}}
    <script>
        lucide.createIcons();
    </script>

    @stack('scripts')
</body>

</html>
