<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
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
            <a href="{{ $url }}" target="_blank"
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Abrir Documento
            </a>
        </div>
    @endif
</x-dynamic-component>
