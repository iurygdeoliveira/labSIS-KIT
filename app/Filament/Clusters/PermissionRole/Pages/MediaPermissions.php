<?php

declare(strict_types=1);

namespace App\Filament\Clusters\PermissionRole\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

class MediaPermissions extends BasePermissionsPage
{
    protected string $view = 'filament.pages.access-control.media-permissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Mídia';

    protected static ?int $navigationSort = 3;

    public function getResourceName(): string
    {
        return 'media';
    }
}
