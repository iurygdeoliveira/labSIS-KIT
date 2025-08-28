@php
    $record = $getRecord();
    $type = 'unknown';
    $canLoad = false;

    if ($record) {
        $disk = $record->disk;
        
        // Verificar se a mídia pode ser carregada
        if ($disk === 'public') {
            $canLoad = true;
        }
        
        // Determinar o tipo de arquivo
        $mimeType = $record->mime_type;
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $type = 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $type = 'audio';
        } elseif (str_starts_with($mimeType, 'application/')) {
            $type = 'document';
        }
    }
@endphp

<div class="space-y-4">
    @if(!$record)
        {{-- Nenhuma mídia selecionada --}}
        <div class="text-center">
            <div class="inline-block max-w-md p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Nenhuma mídia selecionada</p>
            </div>
        </div>
    @elseif(!$canLoad)
        {{-- Mídia não pode ser carregada --}}
        <div class="text-center">
            <div class="inline-block max-w-md p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-2 border-dashed border-red-300 dark:border-red-600">
                <x-icon name="alert-triangle" class="mx-auto h-16 w-16 text-red-500" />
                <p class="mt-2 text-sm font-medium text-red-800 dark:text-red-200">Mídia não pode ser carregada</p>
                <p class="mt-1 text-xs text-red-600 dark:text-red-300">{{ $record->file_name }}</p>
                <p class="mt-1 text-xs text-red-500 dark:text-red-400">Disk: {{ $disk ?? 'N/A' }}</p>
            </div>
        </div>
    @else
        {{-- Renderizar mídia baseada no tipo usando views específicas --}}
        @if($type === 'image')
            @include('filament.components.media-preview.image')
        @elseif($type === 'video')
            @include('filament.components.media-preview.video')
        @elseif($type === 'audio')
            @include('filament.components.media-preview.audio')
        @elseif($type === 'document')
            @include('filament.components.media-preview.document')
        @else
            @include('filament.components.media-preview.unknown')
        @endif
    @endif
</div>
