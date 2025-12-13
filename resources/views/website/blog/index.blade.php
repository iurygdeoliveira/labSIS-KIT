@extends('home.layouts.app')

@section('content')
{{-- Hero Section do Blog --}}
<section class="relative mx-2 my-4 overflow-hidden bg-linear-to-br from-emerald-900 via-emerald-700 to-emerald-900 rounded-3xl sm:mx-4 sm:my-8">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23F2F2F0\' fill-opacity=\'0.4\'%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'2\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="relative px-6 py-16 mx-auto max-w-7xl sm:py-24">
        <div class="text-center">
            <h1 class="mb-6 text-4xl font-bold text-white sm:text-5xl lg:text-6xl">
                Blog LabSIS
            </h1>
            <p class="max-w-2xl mx-auto mb-8 text-lg text-gray-100 sm:text-xl">
                Artigos, tutoriais e novidades sobre tecnologia, desenvolvimento e inovação
            </p>
            
            {{-- Barra de Busca --}}
            <form action="{{ route('blog.search') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Buscar artigos..."
                        class="w-full px-6 py-4 text-gray-900 bg-white border-0 rounded-full shadow-lg focus:ring-4 focus:ring-emerald-500/50 focus:outline-none"
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
    </div>
</section>

{{-- Categorias --}}
@php
    $categories = \App\Models\Category::active()->ordered()->get();
@endphp

@if($categories->isNotEmpty())
<section class="px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900">Categorias</h2>
        </div>
        
        <div class="flex flex-wrap gap-3">
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

{{-- Posts em Destaque --}}
@php
    $featuredPosts = \App\Models\Post::published()->featured()->with(['author', 'categories'])->take(3)->get();
@endphp

@if($featuredPosts->isNotEmpty())
<section class="px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900">Posts em Destaque</h2>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($featuredPosts as $post)
                @include('home.blog.partials.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Todos os Posts --}}
<section class="px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900">Últimos Posts</h2>
        </div>

        @if($posts->isEmpty())
            <div class="py-16 text-center bg-gray-50 rounded-2xl">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xl text-gray-600">Nenhum post publicado ainda.</p>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    @include('home.blog.partials.post-card', ['post' => $post])
                @endforeach
            </div>

            {{-- Paginação --}}
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</section>

{{-- Footer CTA --}}
<section class="mx-4 my-8 overflow-hidden bg-linear-to-br from-emerald-900 to-emerald-700 rounded-3xl">
    <div class="px-6 py-12 text-center">
        <h2 class="mb-4 text-3xl font-bold text-white">
            Fique por dentro das novidades
        </h2>
        <p class="max-w-2xl mx-auto mb-8 text-lg text-gray-100">
            Acompanhe nossos artigos sobre tecnologia, desenvolvimento e inovação
        </p>
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium transition-colors duration-200 bg-white rounded-lg text-emerald-900 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar ao Início
        </a>
    </div>
</section>
@endsection
