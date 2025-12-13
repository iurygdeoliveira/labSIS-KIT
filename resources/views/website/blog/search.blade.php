@extends('home.layouts.app')

@section('content')
{{-- Breadcrumb --}}
<nav class="px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <ol class="flex items-center gap-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Início</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('blog.index') }}" class="hover:text-emerald-700 transition-colors">Blog</a>
            </li>
            <li>/</li>
            <li class="text-gray-900 font-medium">Busca</li>
        </ol>
    </div>
</nav>

{{-- Header de Busca --}}
<section class="px-4 py-12">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-emerald-100 rounded-full">
                <svg class="w-10 h-10 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <h1 class="mb-4 text-4xl font-bold text-gray-900 sm:text-5xl">
                Resultados da Busca
            </h1>
            
            @if($search)
                <p class="mb-6 text-lg text-gray-600">
                    Você buscou por: <span class="font-semibold text-emerald-700">"{{ $search }}"</span>
                </p>
            @endif

            <div class="mb-6">
                <span class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                    {{ $posts->total() }} {{ Str::plural('resultado', $posts->total()) }} {{ Str::plural('encontrado', $posts->total()) }}
                </span>
            </div>
        </div>

        {{-- Barra de Busca --}}
        <form action="{{ route('blog.search') }}" method="GET" class="max-w-2xl mx-auto mb-12">
            <div class="relative">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}"
                    placeholder="Buscar artigos..."
                    class="w-full px-6 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-full focus:ring-4 focus:ring-emerald-500/50 focus:border-emerald-500 focus:outline-none"
                >
                <button 
                    type="submit"
                    class="absolute px-6 py-2 text-white transition-colors duration-200 rounded-full right-2 top-2 bg-emerald-700 hover:bg-emerald-800"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</section>

{{-- Resultados --}}
<section class="px-4 py-8">
    <div class="max-w-7xl mx-auto">
        @if($posts->isEmpty())
            <div class="py-16 text-center bg-gray-50 rounded-2xl">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mb-2 text-xl text-gray-600">Nenhum resultado encontrado</p>
                <p class="mb-6 text-gray-500">Tente usar palavras-chave diferentes ou mais genéricas</p>
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white transition-colors duration-200 rounded-lg bg-emerald-700 hover:bg-emerald-800">
                        Ver todos os posts
                    </a>
                    <button onclick="document.querySelector('input[name=q]').focus()" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-emerald-700 transition-colors duration-200 bg-white border-2 border-emerald-700 rounded-lg hover:bg-emerald-50">
                        Nova busca
                    </button>
                </div>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    @include('home.blog.partials.post-card', ['post' => $post])
                @endforeach
            </div>

            {{-- Paginação --}}
            <div class="mt-12">
                {{ $posts->appends(['q' => $search])->links() }}
            </div>
        @endif
    </div>
</section>

{{-- Sugestões de Categorias --}}
@php
    $categories = \App\Models\Category::active()->ordered()->take(8)->get();
@endphp

@if($categories->isNotEmpty() && $posts->isEmpty())
<section class="px-4 py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="mb-6 text-2xl font-bold text-gray-900 text-center">Explore por categoria</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($categories as $category)
                <a 
                    href="{{ route('blog.category', $category->slug) }}"
                    class="px-6 py-3 text-sm font-medium text-white transition-all duration-200 rounded-full hover:shadow-lg hover:-translate-y-0.5"
                    style="background-color: {{ $category->color ?? '#10b981' }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="px-4 py-8">
    <div class="max-w-7xl mx-auto text-center">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium text-white transition-colors duration-200 rounded-lg bg-emerald-700 hover:bg-emerald-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar ao Blog
        </a>
    </div>
</section>
@endsection
