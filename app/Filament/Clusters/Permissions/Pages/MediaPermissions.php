<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

use Filament\Support\Icons\Heroicon;

class MediaPermissions extends BasePermissionPage
{
    protected string $view = 'filament.clusters.permissions.pages.media-permissions';

    protected static ?string $navigationLabel = 'Mídias';

    protected static ?string $title = 'Permissões de Mídias';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Photo;

    protected static string $resourceSlug = 'media';
}
