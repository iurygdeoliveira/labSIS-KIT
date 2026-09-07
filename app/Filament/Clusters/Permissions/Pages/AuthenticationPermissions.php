<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

use App\Enums\Permission;

class AuthenticationPermissions extends BasePermissionPage
{
    protected string $view = 'panels.clusters.permissions.pages.authentication-permissions';

    protected static ?string $navigationLabel = 'Autenticações';

    protected static ?string $title = 'Permissões de Autenticações';

    protected static string|\BackedEnum|null $navigationIcon = 'icon-admin';

    protected static string $resourceSlug = 'authentication-log';

    #[\Override]
    protected function getAvailableActions(): array
    {
        return [Permission::VIEW];
    }
}
