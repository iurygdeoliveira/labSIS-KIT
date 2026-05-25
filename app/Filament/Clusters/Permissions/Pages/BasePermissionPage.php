<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

use App\Enums\Permission;
use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Filament\Clusters\Permissions\PermissionsCluster;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
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
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        $sectionSchema = [
            Select::make('selectedRole')
                ->label('Tipo de usuário')
                ->options([
                    RoleType::USER->value => RoleType::USER->getLabel(),
                    RoleType::OWNER->value => RoleType::OWNER->getLabel(),
                ])
                ->native(false)
                ->required()
                ->live(),
        ];

        // Toggle mestre só aparece para Admin
        if ($isAdmin) {
            $sectionSchema[] = ToggleForm::make('selectAll')
                ->label('Habilitar todas as permissões em todos os Teams')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon(Heroicon::Check)
                ->offIcon(Heroicon::XMark)
                ->inline(false)
                ->dehydrated(false)
                ->live()
                ->afterStateUpdated(function (bool $state): void {
                    $this->toggleAll($state);
                });
        }

        $schemaComponents = [
            Section::make('Configurações')
                ->columns(2)
                ->schema($sectionSchema),
        ];

        return $schema->schema($schemaComponents);
    }

    protected function getAvailableActions(): array
    {
        return [PermissionEnum::VIEW, PermissionEnum::CREATE, PermissionEnum::UPDATE, PermissionEnum::DELETE];
    }

    public function table(Table $table): Table
    {
        $slug = static::$resourceSlug;
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        /** @var Team|null $currentTeam */
        $currentTeam = Filament::getTenant();

        $query = Team::query()
            ->where('is_active', true)
            ->select(['id', 'name']);

        // Se não for admin, filtra apenas o team atual
        if (! $isAdmin && $currentTeam) {
            $query->where('id', $currentTeam->id);
        }

        $columns = [
            TextColumn::make('name')
                ->label('Team')
                ->when($isAdmin, fn ($column): TextColumn => $column->searchable(isIndividual: true, isGlobal: false)),
        ];

        // Coluna "Todas" só aparece para Admin e se houver mais de uma ação
        if ($isAdmin && count($this->getAvailableActions()) > 1) {
            $columns[] = ToggleColumn::make('all')
                ->label('Todas')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon(Heroicon::Check)
                ->offIcon(Heroicon::XMark)
                ->getStateUsing(
                    fn (Team $record): bool => $this->rowAllEnabled($record->id, $slug)
                )
                ->updateStateUsing(
                    fn (bool $state, Team $record) => tap($state, fn () => $this->setAllPermissionsForTeam($record->id, $slug, $state))
                );
        }

        foreach ($this->getAvailableActions() as $action) {
            $columns[] = ToggleColumn::make($action->value)
                ->label($action->getLabel())
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon(Heroicon::Check)
                ->offIcon(Heroicon::XMark)
                ->getStateUsing(
                    fn (Team $record): bool => $this->hasPermission($record->id, $slug, $action)
                )
                ->updateStateUsing(
                    fn (bool $state, Team $record) => $this->setPermission($record->id, $slug, $action, $state)
                );
        }

        return $table
            ->query($query)
            ->columns($columns);
    }

    protected function hasPermission(int $teamId, string $resourceSlug, PermissionEnum $action): bool
    {
        if ($this->selectedRole === null || ! $this->isAuthorizedForTeam($teamId)) {
            return false;
        }

        resolve(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        $role = $this->resolveRole($teamId);

        return $role->hasPermissionTo("{$resourceSlug}.{$action->value}", $this->guard);
    }

    protected function setPermission(int $teamId, string $resourceSlug, PermissionEnum $action, bool $enabled): void
    {
        if ($this->selectedRole === null || ! $this->isAuthorizedForTeam($teamId)) {
            return;
        }

        resolve(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        $role = $this->resolveRole($teamId);
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

    protected function isAuthorizedForTeam(int $teamId): bool
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        $team = Team::find($teamId);

        return $team instanceof Team && $user->isOwnerOfTeam($team);
    }

    protected function toggleAll(bool $state): void
    {
        $slug = static::$resourceSlug;

        Team::query()
            ->where('is_active', true)
            ->pluck('id')
            ->each(function ($teamId) use ($slug, $state): void {
                foreach ($this->getAvailableActions() as $action) {
                    $this->setPermission((int) $teamId, $slug, $action, $state);
                }
            });

        $this->selectAll = $state;

        $this->dispatch('refreshTable');
    }

    public function rowAllEnabled(int $teamId, string $resourceSlug): bool
    {
        return array_all($this->getAvailableActions(), fn (Permission $action): bool => $this->hasPermission($teamId, $resourceSlug, $action));
    }

    public function setAllPermissionsForTeam(int $teamId, string $resourceSlug, bool $enabled): void
    {
        foreach ($this->getAvailableActions() as $action) {
            $this->setPermission($teamId, $resourceSlug, $action, $enabled);
        }

        $this->dispatch('refreshTable');
    }

    protected function resolveRole(int $teamId): Role
    {
        if ($this->selectedRole === RoleType::OWNER->value) {
            return RoleType::ensureOwnerRoleForTeam($teamId, $this->guard);
        }

        return RoleType::ensureUserRoleForTeam($teamId, $this->guard);
    }
}
