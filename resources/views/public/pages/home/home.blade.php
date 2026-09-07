<div>
    {{-- Header --}}
    <x-public::landing.header />

    {{-- Main Content --}}
    <main class="flex-grow pt-32 relative w-full flex flex-col items-center">
        {{-- Hero Background Grid --}}
        <div class="bg-grid"></div>

        <x-public::landing.hero />

        <x-public::landing.diferenciais />

        <x-public::landing.projetos />

        <x-public::landing.ecossistema />

        <x-public::landing.stats
            :totalProjects="$this->totalProjects"
            :totalAllProjects="$this->totalAllProjects"
            :totalAllDevelopers="$this->totalAllDevelopers"
        />

        <x-public::landing.cta />
    </main>

    {{-- Footer --}}
    <x-public::landing.footer />

    {{-- Filament Action Modals --}}
    <x-filament-actions::modals />
</div>
