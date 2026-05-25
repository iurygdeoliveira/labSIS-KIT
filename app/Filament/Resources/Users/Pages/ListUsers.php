<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\UsersStats;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::Plus),
        ];
    }

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            UsersStats::class,
        ];
    }

    #[\Override]
    public function getTabs(): array
    {
        return [
            'aprovados' => Tab::make('Aprovados')
                ->modifyQueryUsing(fn ($query) => $query->where('is_approved', true))
                ->badge(fn (): string => (string) User::query()->where('is_approved', true)
                    ->withoutRole(RoleType::ADMIN->value)->count('*')),

            'aguardando' => Tab::make('Não aprovados')
                ->modifyQueryUsing(fn ($query) => $query->where('is_approved', false))
                ->badge(fn (): string => (string) User::query()->where('is_approved', false)
                    ->withoutRole(RoleType::ADMIN->value)->count('*'))
                ->badgeColor('danger'),
        ];
    }
}
