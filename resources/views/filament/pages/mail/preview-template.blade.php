<x-filament-panels::page>
    <div class="w-full bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" style="height: calc(100vh - 12rem);">
        <iframe
            srcdoc="{{ $previewHtml }}"
            class="w-full h-full border-0"
            sandbox="allow-same-origin"
        ></iframe>
    </div>
</x-filament-panels::page>
