<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole;

use BackedEnum;
use Filament\Clusters\Cluster;
use UnitEnum;

class UserRoleCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'icon-role';

    protected static ?string $navigationLabel = 'Funções';

    protected static string|UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?string $title = '';

    protected static ?int $navigationSort = 2;
}
