<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $embedUrl = $getState();
    @endphp

    @if (blank($embedUrl))
        <div class="text-center">
            <div class="inline-block max-w-xl w-full p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                <x-icon name="alert-triangle" class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Conteúdo não carregado</p>
            </div>
        </div>
    @else
        <div class="aspect-video w-full max-w-3xl mx-auto rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <iframe
                class="w-full h-full"
                src="{{ $embedUrl }}"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen>
            </iframe>
        </div>
    @endif
</x-dynamic-component>
