<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class PermissionsCluster extends Cluster
{
    protected static ?string $title = '';

    protected static string|\UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Permissões';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;
}
