<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\UsersStats;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            UsersStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'aprovados' => Tab::make('Aprovados')
                ->modifyQueryUsing(fn ($query) => $query->where('is_approved', true))
                ->badge(fn () => User::where('is_approved', true)
                    ->withoutRole(RoleType::ADMIN->value)->count()),

            'aguardando' => Tab::make('Não aprovados')
                ->modifyQueryUsing(fn ($query) => $query->where('is_approved', false))
                ->badge(fn () => User::where('is_approved', false)
                    ->withoutRole(RoleType::ADMIN->value)->count())
                ->badgeColor('danger'),
        ];
    }
}
