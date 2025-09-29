<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Filament\Clusters\Permissions\PermissionsCluster;
use App\Models\Role;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle as ToggleForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

abstract class BasePermissionPage extends Page implements Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $cluster = PermissionsCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    /** @var string Slug do resource: ex. 'users', 'media' */
    protected static string $resourceSlug;

    public ?string $selectedRole = null; // apenas USER inicialmente

    public string $guard = 'web';

    public bool $selectAll = false;

    public function mount(): void
    {
        $this->guard = config('auth.defaults.guard', 'web');
        // Apenas "Usuário Comum" no select.
        $this->selectedRole = RoleType::USER->value;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configurações')
                ->columns(2)
                ->schema([
                    Select::make('selectedRole')
                        ->label('Tipo de usuário')
                        ->options([
                            RoleType::USER->value => RoleType::USER->getLabel(),
                        ])
                        ->native(false)
                        ->required()
                        ->reactive(),

                    ToggleForm::make('selectAll')
                        ->label('Habilitar todas as permissões em todos os Tenants')
                        ->onColor('primary')
                        ->offColor('danger')
                        ->onIcon('heroicon-c-check')
                        ->offIcon('heroicon-c-x-mark')
                        ->dehydrated(false)
                        ->reactive()
                        ->afterStateUpdated(function (bool $state): void {
                            $this->toggleAll($state);
                        }),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        $slug = static::$resourceSlug;

        return $table
            ->query(
                Tenant::query()
                    ->where('is_active', true)
                    ->select(['id', 'name'])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Tenant')
                    ->searchable(isIndividual: true, isGlobal: false),

                ToggleColumn::make('all')
                    ->label('Todas')
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(
                        fn (Tenant $record): bool => $this->rowAllEnabled($record->id, $slug)
                    )
                    ->updateStateUsing(
                        fn (bool $state, Tenant $record) => tap($state, fn () => $this->setAllPermissionsForTenant($record->id, $slug, $state))
                    ),

                ToggleColumn::make('view')
                    ->label(PermissionEnum::VIEW->getLabel())
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(
                        fn (Tenant $record): bool => $this->hasPermission($record->id, $slug, PermissionEnum::VIEW)
                    )
                    ->afterStateUpdated(
                        fn (Tenant $record, bool $state) => $this->setPermission($record->id, $slug, PermissionEnum::VIEW, $state)
                    ),

                ToggleColumn::make('create')
                    ->label(PermissionEnum::CREATE->getLabel())
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(
                        fn (Tenant $record): bool => $this->hasPermission($record->id, $slug, PermissionEnum::CREATE)
                    )
                    ->afterStateUpdated(
                        fn (Tenant $record, bool $state) => $this->setPermission($record->id, $slug, PermissionEnum::CREATE, $state)
                    ),

                ToggleColumn::make('update')
                    ->label(PermissionEnum::UPDATE->getLabel())
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(
                        fn (Tenant $record): bool => $this->hasPermission($record->id, $slug, PermissionEnum::UPDATE)
                    )
                    ->afterStateUpdated(
                        fn (Tenant $record, bool $state) => $this->setPermission($record->id, $slug, PermissionEnum::UPDATE, $state)
                    ),

                ToggleColumn::make('delete')
                    ->label(PermissionEnum::DELETE->getLabel())
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(
                        fn (Tenant $record): bool => $this->hasPermission($record->id, $slug, PermissionEnum::DELETE)
                    )
                    ->afterStateUpdated(
                        fn (Tenant $record, bool $state) => $this->setPermission($record->id, $slug, PermissionEnum::DELETE, $state)
                    ),
            ]);
    }

    protected function hasPermission(int $tenantId, string $resourceSlug, PermissionEnum $action): bool
    {
        if ($this->selectedRole === null) {
            return false;
        }

        // Para USER, usar contexto de team (tenant). Proprietário não aparece; Admin não aparece nesta tela.
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);

        $role = $this->resolveRole($tenantId);

        return $role->hasPermissionTo("{$resourceSlug}.{$action->value}", $this->guard);
    }

    protected function setPermission(int $tenantId, string $resourceSlug, PermissionEnum $action, bool $enabled): void
    {
        if ($this->selectedRole === null) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);

        $role = $this->resolveRole($tenantId);
        $permissionName = "{$resourceSlug}.{$action->value}";

        SpatiePermission::findOrCreate($permissionName, $this->guard);

        if ($enabled) {
            if (! $role->hasPermissionTo($permissionName, $this->guard)) {
                $role->givePermissionTo($permissionName);
            }

            return;
        }

        if ($role->hasPermissionTo($permissionName, $this->guard)) {
            $role->revokePermissionTo($permissionName);
        }
    }

    protected function toggleAll(bool $state): void
    {
        $slug = static::$resourceSlug;

        Tenant::query()
            ->where('is_active', true)
            ->pluck('id')
            ->each(function ($tenantId) use ($slug, $state) {
                foreach ([PermissionEnum::VIEW, PermissionEnum::CREATE, PermissionEnum::UPDATE, PermissionEnum::DELETE] as $action) {
                    $this->setPermission((int) $tenantId, $slug, $action, $state);
                }
            });

        $this->selectAll = $state;

        $this->dispatch('refreshTable');
    }

    public function rowAllEnabled(int $tenantId, string $resourceSlug): bool
    {
        foreach ([PermissionEnum::VIEW, PermissionEnum::CREATE, PermissionEnum::UPDATE, PermissionEnum::DELETE] as $action) {
            if (! $this->hasPermission($tenantId, $resourceSlug, $action)) {
                return false;
            }
        }

        return true;
    }

    public function setAllPermissionsForTenant(int $tenantId, string $resourceSlug, bool $enabled): void
    {
        foreach ([PermissionEnum::VIEW, PermissionEnum::CREATE, PermissionEnum::UPDATE, PermissionEnum::DELETE] as $action) {
            $this->setPermission($tenantId, $resourceSlug, $action, $enabled);
        }

        $this->dispatch('refreshTable');
    }

    protected function resolveRole(int $tenantId): Role
    {
        // Nesta tela só aparece USER, então garantir a role de USER no team
        return RoleType::ensureUserRoleForTeam($tenantId, $this->guard);
    }
}
