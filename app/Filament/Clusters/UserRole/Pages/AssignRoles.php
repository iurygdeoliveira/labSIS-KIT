<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\TenantUser;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class AssignRoles extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected string $view = 'filament.clusters.user-role.pages.assign-roles';

    protected static ?string $cluster = UserRoleCluster::class;

    protected static ?string $title = 'Atribuir Funções';

    protected static ?string $navigationLabel = 'Atribuir Funções';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TenantUser::query()
                    ->with(['user', 'tenant'])
                    ->whereHas(
                        'user',
                        fn ($query) => $query->when(
                            Filament::auth()->check(),
                            fn ($query) => $query->whereKeyNot(Filament::auth()->id())
                        )
                    )
            )
            ->groups([
                Group::make('tenant.name')
                    ->label('Tenant'),
            ])
            ->defaultGroup('tenant.name')
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuário'),
                TextColumn::make('user.email')
                    ->label('E-mail'),
                ToggleColumn::make('role_user')
                    ->label(RoleType::USER->value)
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(static function (TenantUser $record): bool {
                        return $record->user->roles()
                            ->where('name', RoleType::USER->value)
                            ->where('roles.team_id', $record->tenant_id)
                            ->exists();
                    })
                    ->updateStateUsing(static function (TenantUser $record, bool $state): void {
                        if ($state) {
                            $roleUser = RoleType::ensureUserRoleForTeam($record->tenant_id, 'web');
                            $record->user->assignRole($roleUser);
                        } else {
                            $record->user->roles()
                                ->where('name', RoleType::USER->value)
                                ->where('roles.team_id', $record->tenant_id)
                                ->detach();
                        }
                    }),
            ]);
    }
}
