<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detalhes da Organização')
                    ->columnSpanFull()
                    ->tabs([
                        self::getGeneralInfoTab(),
                        self::getTechnicalDetailsTab(),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }

    private static function getGeneralInfoTab(): Tab
    {
        return Tab::make('Informações Gerais')
            ->icon(Heroicon::BuildingOffice)
            ->schema([
                TextEntry::make('name')
                    ->label(__('organization.fields.name'))
                    ->icon(Heroicon::BuildingOffice)
                    ->columnSpan(1),

                TextEntry::make('slug')
                    ->label(__('organization.fields.slug'))
                    ->columnSpan(1),

                TextEntry::make('users_count')
                    ->label(__('organization.members.title'))
                    ->state(fn ($record): int => $record->users()->count())
                    ->columnSpan(1),
            ])
            ->columns(2);
    }

    private static function getTechnicalDetailsTab(): Tab
    {
        return Tab::make('Detalhes Técnicos')
            ->icon(Heroicon::Cog6Tooth)
            ->schema([
                TextEntry::make('created_at')
                    ->label(__('organization.fields.created_at'))
                    ->dateTime('d/m/Y H:i'),

                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->columns(2);
    }
}
