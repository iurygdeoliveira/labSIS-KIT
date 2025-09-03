<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        /** @var \App\Models\Media|null $record */
        $record = $record ?? null;
        $url = null;

        if ($record && $record->disk === 'public') {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($record->file_name)) {
                $url = asset('storage/'.$record->file_name);
            }
        }
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
            <div class="inline-block max-w-md p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                <img src="{{ $url }}" 
                     alt="{{ $record?->name ?? 'Imagem' }}" 
                     class="max-w-full h-auto rounded-lg shadow-sm">
            </div>
        </div>
    @endif
</x-dynamic-component>
