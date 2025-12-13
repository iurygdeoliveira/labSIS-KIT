{{-- resources/views/partials/head.blade.php --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>@yield('title', 'LabSIS - Laboratório de Sistemas Inovadores')</title>

<meta name="description" content="@yield('description', 'LabSIS - Laboratório de Sistemas Inovadores. Softwares que transformam a realidade !')">
<meta name="author" content="LabSIS">
<meta name="keywords" content="LabSIS, laboratório, sistemas de informação, desenvolvimento, software, tecnologia, inovação, Laravel, Vue.js, React, Next.js">

{{-- Favicon --}}
<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'LabSIS - Laboratório de Sistemas Inovadores')">
<meta property="og:description" content="@yield('description', 'LabSIS - Laboratório de Sistemas Inovadores. Softwares que transformam a realidade !')">
<meta property="og:image" content="{{ asset('images/og-image.png') }}">
<meta property="og:site_name" content="LabSIS">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="@yield('title', 'LabSIS - Laboratório de Sistemas Inovadores')">
<meta name="twitter:description" content="@yield('description', 'LabSIS - Laboratório de Sistemas Inovadores. Softwares que transformam a realidade !')">
<meta name="twitter:image" content="{{ asset('images/og-image.png') }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

@stack('head')
