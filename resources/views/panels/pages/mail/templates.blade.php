<x-filament-panels::page>
    {{ $this->table }}

    <x-filament::modal id="code-modal" width="5xl">
        <x-slot name="heading">
            {{ $codeModalTitle }}
        </x-slot>
        <div class="p-4 bg-gray-50 border rounded-lg dark:bg-gray-900 dark:border-white/10 overflow-auto max-h-[600px]">
            <pre><code class="language-blade">{{ $codeModalContent }}</code></pre>
        </div>
    </x-filament::modal>
</x-filament-panels::page>

