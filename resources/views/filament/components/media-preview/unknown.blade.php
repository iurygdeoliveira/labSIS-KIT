@php
    $record = $getRecord();
@endphp

<div class="text-center">
    <div class="inline-block max-w-md p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
        <div class="flex flex-col items-center">
            <svg class="h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $record->file_name }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">{{ $record->mime_type }}</p>
        </div>
    </div>
</div>
