<?php

declare(strict_types=1);

namespace App\Support\Changelog;

use App\Filament\Pages\ChangelogPage;
use App\Filament\Resources\Changelog\ChangelogResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ChangelogPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'changelog';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ChangelogResource::class,
            ])
            ->pages([
                ChangelogPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function navigationGroup(?string $group): static
    {
        return $this;
    }

    public function navigationLabel(?string $label): static
    {
        return $this;
    }

    public function canManage(mixed $callback): static
    {
        return $this;
    }
}
