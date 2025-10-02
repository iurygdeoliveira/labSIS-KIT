<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RoleType;
use App\Models\Role;
use App\Models\User;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User Details')
                    ->columnSpanFull()
                    ->tabs([
                        self::getPersonalInfoTab(),
                        self::getDatesTab(),
                        self::getSuspensionTab(),
                        self::getTenantsTab(),
                    ])->persistTabInQueryString(),
            ]);
    }

    private static function getPersonalInfoTab(): Tab
    {
        return Tab::make('Informações Pessoais')
            ->icon('icon-userpersonal')
            ->schema([
                TextEntry::make('name')
                    ->label('Nome'),
                TextEntry::make('email')
                    ->label('E-mail'),
            ])->columns(2);
    }

    private static function getDatesTab(): Tab
    {
        return Tab::make('Datas')
            ->icon(Heroicon::Calendar)
            ->schema([
                TextEntry::make('email_verified_at')
                    ->label('E-mail Verificado em')
                    ->dateTime('d-m-Y H:i'),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d-m-Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d-m-Y H:i'),
            ])->columns(2);
    }

    private static function getSuspensionTab(): Tab
    {
        return Tab::make('Suspensão')
            ->icon(Heroicon::NoSymbol)
            ->schema([
                TextEntry::make('is_suspended')
                    ->label('Status')
                    ->formatStateUsing(fn (?bool $state): string => $state ? __('Suspenso') : __('Autorizado'))
                    ->badge()
                    ->color(fn (?bool $state): string => $state ? 'danger' : 'primary')
                    ->icon(fn (?bool $state): string => $state ? 'heroicon-c-no-symbol' : 'heroicon-c-check'),
                TextEntry::make('suspended_at')
                    ->label('Suspenso em')
                    ->dateTime('d-m-Y H:i')
                    ->placeholder('-'),
                TextEntry::make('suspension_reason')
                    ->label('Motivo da suspensão')
                    ->placeholder('-'),
            ]);
    }

    private static function getTenantsTab(): Tab
    {
        return Tab::make('Tenants')
            ->icon(Heroicon::BuildingOffice)
            ->schema([
                RepeatableEntry::make('tenants_roles')
                    ->hiddenLabel()
                    ->visible(fn (?User $record): bool => (bool) $record?->tenants()->exists())
                    ->state(fn (?User $record): array => self::getTenantsRolesState($record))
                    ->schema([
                        TextEntry::make('tenant')
                            ->label('Tenant'),
                        TextEntry::make('roles')
                            ->label('Funções')
                            ->badge(),
                    ])
                    ->columns(2),
                TextEntry::make('no_tenants')
                    ->label('')
                    ->state('Usuário não associado a nenhum tenant')
                    ->visible(fn (?User $record): bool => ! (bool) $record?->tenants()->exists()),
            ]);
    }

    private static function getTenantsRolesState(?User $record): array
    {
        if (! $record) {
            return [];
        }

        $items = [];
        $tenants = $record->tenants()->select(['tenants.id', 'tenants.name'])->get();

        foreach ($tenants as $tenant) {
            $roleNames = Role::query()
                ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'roles.id')
                ->where('mhr.model_type', User::class)
                ->where('mhr.model_id', $record->id)
                ->where('mhr.team_id', $tenant->id)
                ->pluck('roles.name')
                ->all();

            $labels = [];
            foreach ($roleNames as $name) {
                try {
                    $labels[] = RoleType::from($name)->getLabel();
                } catch (\ValueError) {
                    $labels[] = $name;
                }
            }

            $items[] = [
                'tenant' => (string) $tenant->name,
                'roles' => empty($labels) ? 'sem função' : implode(', ', $labels),
            ];
        }

        return $items;
    }
}
