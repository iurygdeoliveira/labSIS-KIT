<?php

declare(strict_types=1);

namespace App\Filament\Clusters\PermissionRole\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

class UserPermissions extends BasePermissionsPage
{
    protected string $view = 'filament.pages.access-control.user-permissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?int $navigationSort = 2;

    public function getResourceName(): string
    {
        return 'users';
    }
}
