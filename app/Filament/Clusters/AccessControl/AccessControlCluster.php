<?php

declare(strict_types=1);

namespace App\Filament\Clusters\AccessControl;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AccessControlCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Permissões';

    protected static string|UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?string $title = '';

    protected static ?int $navigationSort = 1;
}
