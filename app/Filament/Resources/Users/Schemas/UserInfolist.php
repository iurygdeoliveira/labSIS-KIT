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
                        Tab::make('Datas')
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
                            ])->columns(2),
                        Tab::make('Suspensão')
                            ->icon(Heroicon::NoSymbol)
                            ->schema([
                                TextEntry::make('is_suspended')
                                    ->label('Status')
                                    ->formatStateUsing(fn (?bool $state): string => $state ? __('Suspenso') : __('Autorizado'))
                                    ->badge()
                                    ->color(fn (?bool $state): string => $state ? 'danger' : 'success')
                                    ->icon(fn (?bool $state): string => $state ? 'heroicon-c-no-symbol' : 'heroicon-c-check'),
                                TextEntry::make('suspended_at')
                                    ->label('Suspenso em')
                                    ->dateTime('d-m-Y H:i')
                                    ->placeholder('-'),
                                TextEntry::make('suspension_reason')
                                    ->label('Motivo da suspensão')
                                    ->placeholder('-'),
                            ]),
                    ])->persistTabInQueryString(),
            ]);
    }
}
