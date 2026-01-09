<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Composer\InstalledVersions;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomStats extends BaseWidget
{
    #[\Override]
    protected function getStats(): array
    {
        return [
            $this->packageStat('laravel/framework', 'Laravel', 'icon-laravel'),
            $this->packageStat('filament/filament', 'FilamentPHP', 'icon-filament'),
            $this->packageStat('livewire/livewire', 'Livewire', 'icon-livewire'),
            $this->getTailwindStat(),
        ];
    }

    private function packageStat(string $package, string $label, string $icon): Stat
    {
        try {
            $version = InstalledVersions::getPrettyVersion($package) ?? 'Unknown';
        } catch (\Throwable) {
            $version = 'Not installed';
        }

        return Stat::make($label, $version)
            ->icon($icon);
    }

    private function getTailwindStat(): Stat
    {
        $version = $this->getTailwindVersion();

        return Stat::make('Tailwind CSS', str_replace('^', 'v', $version))
            ->descriptionIcon('heroicon-m-check-circle')
            ->icon('icon-tailwind');
    }

    private function getTailwindVersion(): string
    {
        $packageJsonPath = base_path('package.json');

        if (! file_exists($packageJsonPath)) {
            return 'Não encontrado';
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        return $packageJson['devDependencies']['tailwindcss'] ?? 'Não instalado';
    }
}
