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
            <li class="text-gray-900 font-medium">{{ $category->name }}</li>
        </ol>
    </div>
</nav>

{{-- Header da Categoria --}}
<section class="px-4 py-12">
    <div class="max-w-7xl mx-auto text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full" style="background-color: {{ $category->color ?? '#10b981' }}15">
            <svg class="w-10 h-10" style="color: {{ $category->color ?? '#10b981' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        
        <h1 class="mb-4 text-4xl font-bold text-gray-900 sm:text-5xl">
            {{ $category->name }}
        </h1>
        
        @if($category->description)
            <p class="max-w-2xl mx-auto text-lg text-gray-600">
                {{ $category->description }}
            </p>
        @endif

        <div class="mt-6">
            <span class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                {{ $posts->total() }} {{ Str::plural('artigo', $posts->total()) }}
            </span>
        </div>
    </div>
</section>

{{-- Posts da Categoria --}}
<section class="px-4 py-8">
    <div class="max-w-7xl mx-auto">
        @if($posts->isEmpty())
            <div class="py-16 text-center bg-gray-50 rounded-2xl">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mb-4 text-xl text-gray-600">Nenhum post nesta categoria ainda.</p>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white transition-colors duration-200 rounded-lg bg-emerald-700 hover:bg-emerald-800">
                    Ver todos os posts
                </a>
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

{{-- Outras Categorias --}}
@php
    $otherCategories = \App\Models\Category::active()
        ->where('id', '!=', $category->id)
        ->ordered()
        ->take(6)
        ->get();
@endphp

@if($otherCategories->isNotEmpty())
<section class="px-4 py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="mb-6 text-2xl font-bold text-gray-900 text-center">Explore outras categorias</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($otherCategories as $otherCategory)
                <a 
                    href="{{ route('blog.category', $otherCategory->slug) }}"
                    class="px-6 py-3 text-sm font-medium text-white transition-all duration-200 rounded-full hover:shadow-lg hover:-translate-y-0.5"
                    style="background-color: {{ $otherCategory->color ?? '#10b981' }}"
                >
                    {{ $otherCategory->name }}
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
            Ver todos os posts
        </a>
    </div>
</section>
@endsection
