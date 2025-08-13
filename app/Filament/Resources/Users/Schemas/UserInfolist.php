<?php

namespace App\Filament\Resources\Users\Schemas;

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
                    ->tabs([
                        Tab::make('Informações Pessoais')
                            ->icon('icon-userpersonal')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nome'),
                                TextEntry::make('email')
                                    ->label('E-mail'),
                            ]),
                        Tab::make('Controle de Datas')
                            ->icon(Heroicon::Calendar)
                            ->schema([
                                TextEntry::make('email_verified_at')
                                    ->label('E-mail Verificado em')
                                    ->dateTime(),
                                TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Atualizado em')
                                    ->dateTime(),
                            ]),
                    ])->persistTabInQueryString(),
            ]);
    }
}
