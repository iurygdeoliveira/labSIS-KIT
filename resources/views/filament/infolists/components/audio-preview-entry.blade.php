<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        /** @var \App\Models\Media|null $record */
        $record = $record ?? null;
        $url = $getState();
    @endphp

    @if (blank($url))
        <div class="text-center">
            <div class="inline-block max-w-xl w-full p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                <x-icon name="alert-triangle" class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Conteúdo não carregado</p>
            </div>
        </div>
    @else
        <div class="text-center">
            <div class="inline-block max-w-md p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <audio controls class="w-full">
                    <source src="{{ $url }}" type="{{ $record?->mime_type ?? 'audio/mpeg' }}">
                    Seu navegador não suporta o elemento de áudio.
                </audio>
            </div>
        </div>
    @endif
</x-dynamic-component>
