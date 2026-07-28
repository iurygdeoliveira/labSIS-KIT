<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Schemas;


use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ChangelogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detalhes do Registro')
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
            ->icon(Heroicon::InformationCircle)
            ->schema([
                TextEntry::make('version')
                    ->label('Versão')
                    ->badge()
                    ->color('gray')
                    ->columnSpan(1),


                TextEntry::make('released_at')
                    ->label('Data de Lançamento')
                    ->date('d/m/Y')
                    ->placeholder('Não lançado')
                    ->columnSpan(1),

                TextEntry::make('description')
                    ->label('Descrição')
                    ->columnSpan(2),
            ])
            ->columns(2);
    }

    private static function getTechnicalDetailsTab(): Tab
    {
        return Tab::make('Detalhes Técnicos')
            ->icon(Heroicon::Cog6Tooth)
            ->schema([
                TextEntry::make('uuid')
                    ->label('Identificador UUID')
                    ->copyable()
                    ->columnSpan(2),

                TextEntry::make('sort')
                    ->label('Ordem de Exibição')
                    ->columnSpan(2),

                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->columnSpan(1),

                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
