@php
    $record = $getRecord();
    $url = asset('storage/'.$record->file_name);
    $mimeType = $record->mime_type;
@endphp

<div class="text-center">
    <div class="inline-block max-w-md p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
        <audio controls class="w-full">
            <source src="{{ $url }}" type="{{ $mimeType }}">
            Seu navegador não suporta o elemento de áudio.
        </audio>
    </div>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $record->file_name }}</p>
</div>
