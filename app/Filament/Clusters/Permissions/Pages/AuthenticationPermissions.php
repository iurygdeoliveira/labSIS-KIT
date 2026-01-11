<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

class AuthenticationPermissions extends BasePermissionPage
{
    protected string $view = 'filament.clusters.permissions.pages.authentication-permissions';

    protected static ?string $navigationLabel = 'Autenticações';

    protected static ?string $title = 'Permissões de Autenticações';

    protected static string|\BackedEnum|null $navigationIcon = 'icon-admin';

    protected static string $resourceSlug = 'authentication-log';

    protected function getAvailableActions(): array
    {
        return [\App\Enums\Permission::VIEW];
    }
}
