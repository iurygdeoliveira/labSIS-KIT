@php
    $record = $getRecord();
    $url = asset('storage/'.$record->file_name);
    $alt = $record->name;
@endphp

<div class="text-center">
    <div class="inline-block max-w-md p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
        <img src="{{ $url }}" 
             alt="{{ $alt }}" 
             class="max-w-full h-auto rounded-lg shadow-sm"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <div class="hidden text-red-500 dark:text-red-400 text-sm mt-2">
            <x-icon name="alert-triangle" class="mx-auto h-12 w-12 text-red-500" />
            <p class="mt-2">Imagem não pode ser carregada</p>
        </div>
    </div>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $record->file_name }}</p>
</div>
