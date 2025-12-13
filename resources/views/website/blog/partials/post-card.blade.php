{{-- Post Card Component --}}
<article class="relative overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl group hover:shadow-xl hover:-translate-y-1">
    {{-- Imagem de Capa --}}
    <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden aspect-w-16 aspect-h-9">
        @if($post->hasMedia('cover'))
            <img 
                src="{{ $post->getFirstMediaUrl('cover', 'thumb') }}" 
                alt="{{ $post->title }}"
                class="object-cover w-full h-48 transition-transform duration-300 group-hover:scale-105"
            >
        @else
            <div class="flex items-center justify-center h-48 bg-linear-to-br from-emerald-900 to-emerald-700">
                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
        @endif
    </a>

    {{-- Conteúdo do Card --}}
    <div class="p-6">
        {{-- Categorias --}}
        @if($post->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($post->categories->take(3) as $category)
                    <a 
                        href="{{ route('blog.category', $category->slug) }}"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200"
                        style="background-color: {{ $category->color ?? '#10b981' }}15; color: {{ $category->color ?? '#10b981' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Título --}}
        <a href="{{ route('blog.show', $post->slug) }}" class="block group">
            <h3 class="mb-3 text-xl font-bold text-gray-900 line-clamp-2 group-hover:text-emerald-700 transition-colors duration-200">
                {{ $post->title }}
            </h3>
        </a>

        {{-- Excerpt --}}
        @if($post->excerpt)
            <p class="mb-4 text-gray-600 line-clamp-3">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Meta informações --}}
        <div class="flex items-center justify-between pt-4 text-sm text-gray-500 border-t border-gray-100">
            <div class="flex items-center gap-4">
                {{-- Autor --}}
                @if($post->author)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ $post->author->name }}</span>
                    </div>
                @endif

                {{-- Data --}}
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->format('d/m/Y') }}
                    </time>
                </div>
            </div>

            {{-- Tempo de leitura --}}
            @if($post->reading_time)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $post->reading_time }} min</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Badge de Destaque --}}
    @if($post->featured)
        <div class="absolute top-4 right-4">
            <span class="px-3 py-1 text-xs font-bold text-white bg-emerald-700 rounded-full shadow-lg">
                Destaque
            </span>
        </div>
    @endif
</article>
