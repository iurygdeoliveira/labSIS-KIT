@props([
    'title',
    'url',
])

<div class="flex items-center gap-x-3">
    <a
        href="{{ $url }}"
        title="Voltar"
        class="inline-flex items-center justify-center shrink-0 w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-700 shadow-2xs hover:bg-gray-100/80 focus:outline-hidden focus:ring-2 focus:ring-primary-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 dark:focus:ring-primary-500 transition duration-75"
    >
        <x-filament::icon
            icon="heroicon-m-arrow-left"
            class="w-5 h-5"
        />
    </a>
    <span class="truncate">{{ $title }}</span>
</div>
