<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Traits\Filament\NotificationsTrait;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageUserRoles extends Page implements HasTable
{
    use InteractsWithTable;
    use NotificationsTrait;

    #[\Livewire\Attributes\Url(except: '')]
    public ?string $tenant_id = null;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $title = 'Funções nos Tenants';

    protected static bool $shouldRegisterNavigation = true;

    #[\Override]
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected static ?string $cluster = UserRoleCluster::class;

    /**
     * @return array<NavigationItem>
     */
    #[\Override]
    public static function getNavigationItems(): array
    {
        return Tenant::all()->map(
            fn (Tenant $tenant): \Filament\Navigation\NavigationItem => static::buildTenantNavigationItem($tenant)
        )->toArray();
    }

    #[\Override]
    public function getView(): string
    {
        return 'filament.clusters.user-role.pages.manage-user-roles';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TenantUser::query()
                    ->with(['user', 'tenant'])
                    ->when(
                        $this->tenant_id,
                        fn ($query) => $query->where('tenant_id', $this->tenant_id),
                        fn ($query) => $query->whereNull('id') // Força lista vazia se nenhum tenant selecionado (opcional, ou mostrar todos)
                    )
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nome')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                SelectColumn::make('role_virtual')
                    ->label('Função')
                    ->alignment(Alignment::Center)
                    ->native(false)
                    ->options([
                        'owner' => RoleType::OWNER->getLabel(),
                        'user' => RoleType::USER->getLabel(),
                        'none' => 'Sem função',
                    ])
                    ->state(function (TenantUser $record): string {
                        if ($record->user->isOwnerOfTenant($record->tenant)) {
                            return 'owner';
                        }

                        if ($record->user->isUserOfTenant($record->tenant)) {
                            return 'user';
                        }

                        return 'none';
                    })
                    ->beforeStateUpdated(function (TenantUser $record, string $state): false {
                        $this->assignRole($record, $state === 'none' ? null : $state);

                        // Retornar false para prevenir o save automático do Filament
                        return false;
                    })
                    ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                    ->selectablePlaceholder(false),
            ])
            ->defaultSort('user.name');
    }

    /**
     * Constrói um item de navegação para um tenant específico
     */
    protected static function buildTenantNavigationItem(Tenant $tenant): NavigationItem
    {
        return NavigationItem::make($tenant->name)
            ->icon('heroicon-o-building-office')
            ->isActiveWhen(function () use ($tenant): bool {
                $url = request()->fullUrl();
                $referer = request()->header('referer');

                return str_contains($url, 'tenant_id='.$tenant->id) ||
                       (str_contains($url, 'livewire/update') && str_contains($referer ?? '', 'tenant_id='.$tenant->id));
            })
            ->activeIcon('heroicon-s-building-office')
            ->url(static::getUrl(['tenant_id' => $tenant->id]));
    }

    public function assignRole(TenantUser $record, ?string $newRole): void
    {
        // Remover todas as roles atuais no tenant
        $record->user->removeAllUserRolesFromTenant($record->tenant);
        $record->user->removeAllOwnerRolesFromTenant($record->tenant);

        // Atribuir nova role se não for vazio
        if ($newRole === 'owner') {
            $role = RoleType::ensureOwnerRoleForTeam($record->tenant_id, 'web');
            $record->user->assignRoleInTenant($role, $record->tenant);

            $this->notifySuccess(
                'Função atualizada',
                "Usuário {$record->user->name} agora é Proprietário"
            );
        } elseif ($newRole === 'user') {
            $role = RoleType::ensureUserRoleForTeam($record->tenant_id, 'web');
            $record->user->assignRoleInTenant($role, $record->tenant);

            $this->notifySuccess(
                'Função atualizada',
                "Usuário {$record->user->name} agora é Usuário Comum"
            );
        } else {
            $this->notifySuccess(
                'Função removida',
                "Funções removidas de {$record->user->name}"
            );
        }
    }
}
