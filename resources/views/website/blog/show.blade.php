@extends('home.layouts.app')

@section('content')
{{-- Breadcrumb --}}
<nav class="px-4 py-4">
    <div class="max-w-4xl mx-auto">
        <ol class="flex items-center gap-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Início</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('blog.index') }}" class="hover:text-emerald-700 transition-colors">Blog</a>
            </li>
            <li>/</li>
            <li class="text-gray-900 font-medium truncate">{{ $post->title }}</li>
        </ol>
    </div>
</nav>

{{-- Artigo Principal --}}
<article class="px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header do Post --}}
        <header class="mb-8">
            {{-- Categorias --}}
            @if($post->categories->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($post->categories as $category)
                        <a 
                            href="{{ route('blog.category', $category->slug) }}"
                            class="px-4 py-2 text-sm font-medium text-white rounded-full transition-colors duration-200 hover:opacity-90"
                            style="background-color: {{ $category->color ?? '#10b981' }}"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Título --}}
            <h1 class="mb-6 text-4xl font-bold text-gray-900 sm:text-5xl">
                {{ $post->title }}
            </h1>

            {{-- Excerpt --}}
            @if($post->excerpt)
                <p class="mb-6 text-xl text-gray-600">
                    {{ $post->excerpt }}
                </p>
            @endif

            {{-- Meta informações --}}
            <div class="flex flex-wrap items-center gap-4 pb-6 text-sm text-gray-600 border-b border-gray-200">
                {{-- Autor --}}
                @if($post->author)
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium">{{ $post->author->name }}</span>
                    </div>
                @endif

                {{-- Data --}}
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->format('d/m/Y') }}
                    </time>
                </div>

                {{-- Tempo de leitura --}}
                @if($post->reading_time)
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $post->reading_time }} min de leitura</span>
                    </div>
                @endif

                {{-- Visualizações --}}
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>{{ number_format($post->views_count) }} visualizações</span>
                </div>
            </div>
        </header>

        {{-- Imagem de Capa --}}
        @if($post->hasMedia('cover'))
            <figure class="mb-8 overflow-hidden shadow-2xl rounded-2xl">
                <img 
                    src="{{ $post->getFirstMediaUrl('cover') }}" 
                    alt="{{ $post->title }}"
                    class="object-cover w-full h-auto"
                >
            </figure>
        @endif

        {{-- Conteúdo --}}
        <div class="prose prose-lg prose-emerald max-w-none">
            {!! $post->content !!}
        </div>

        {{-- Vídeos --}}
        @if($post->video_urls && count($post->video_urls) > 0)
            <section class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">Vídeos Relacionados</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach($post->video_urls as $videoUrl)
                        <div class="overflow-hidden shadow-lg aspect-w-16 aspect-h-9 rounded-xl">
                            <iframe 
                                src="{{ $videoUrl }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                class="w-full h-64"
                            ></iframe>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Compartilhar --}}
        <section class="py-8 mt-12 border-t border-gray-200">
            <h3 class="mb-4 text-lg font-bold text-gray-900">Compartilhe este artigo</h3>
            <div class="flex gap-3">
                <a 
                    href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}"
                    target="_blank"
                    class="flex items-center justify-center w-12 h-12 text-white transition-colors duration-200 bg-blue-400 rounded-full hover:bg-blue-500"
                    title="Compartilhar no Twitter"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                    </svg>
                </a>
                <a 
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                    target="_blank"
                    class="flex items-center justify-center w-12 h-12 text-white transition-colors duration-200 bg-blue-600 rounded-full hover:bg-blue-700"
                    title="Compartilhar no Facebook"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                    </svg>
                </a>
                <a 
                    href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.show', $post->slug)) }}&title={{ urlencode($post->title) }}"
                    target="_blank"
                    class="flex items-center justify-center w-12 h-12 text-white transition-colors duration-200 bg-blue-700 rounded-full hover:bg-blue-800"
                    title="Compartilhar no LinkedIn"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
                        <circle cx="4" cy="4" r="2"/>
                    </svg>
                </a>
                <a 
                    href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . route('blog.show', $post->slug)) }}"
                    target="_blank"
                    class="flex items-center justify-center w-12 h-12 text-white transition-colors duration-200 bg-green-500 rounded-full hover:bg-green-600"
                    title="Compartilhar no WhatsApp"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </a>
            </div>
        </section>
    </div>
</article>

{{-- Posts Relacionados --}}
@if($relatedPosts->isNotEmpty())
<section class="px-4 py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="mb-8 text-3xl font-bold text-gray-900">Artigos Relacionados</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($relatedPosts as $relatedPost)
                @include('home.blog.partials.post-card', ['post' => $relatedPost])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Voltar ao Blog --}}
<section class="px-4 py-8">
    <div class="max-w-4xl mx-auto text-center">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium text-white transition-colors duration-200 rounded-lg bg-emerald-700 hover:bg-emerald-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar ao Blog
        </a>
    </div>
</section>
@endsection
