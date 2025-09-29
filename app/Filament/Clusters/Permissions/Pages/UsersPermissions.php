<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

use Filament\Support\Icons\Heroicon;

class UsersPermissions extends BasePermissionPage
{
    protected string $view = 'filament.clusters.permissions.pages.users-permissions';

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?string $title = 'Permissões de Usuários';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static string $resourceSlug = 'users';
}
