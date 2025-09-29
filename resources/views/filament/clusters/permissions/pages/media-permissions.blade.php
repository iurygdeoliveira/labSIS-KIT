@php /** @var \App\Filament\Clusters\Permissions\Pages\MediaPermissions $this */ @endphp

<x-filament::page>
    {{ $this->form }}
    {{ $this->table }}
    <x-slot name="footer">
        {{-- Espaço reservado para ações adicionais no futuro --}}
    </x-slot>
</x-filament::page>


