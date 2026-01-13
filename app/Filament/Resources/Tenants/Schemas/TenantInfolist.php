<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Support\AppDateTime;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Tenant')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nome'),

                        TextEntry::make('is_active')
                            ->label('Status')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Ativo' : 'Inativo')
                            ->badge()
                            ->color(fn ($record): string => (bool) $record->is_active ? 'primary' : 'danger'),

                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->formatStateUsing(fn ($state): ?string => $state ? AppDateTime::parse($state)->format('d/m/Y H:i') : null),

                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->formatStateUsing(fn ($state): ?string => $state ? AppDateTime::parse($state)->format('d/m/Y H:i') : null),
                    ])
                    ->columns(2),

                Section::make('Usuários')
                    ->components([
                        TextEntry::make('users_total')
                            ->label('Total de Usuários')
                            ->state(fn ($record): int => (int) $record->users()->count()),

                        TextEntry::make('users_list')
                            ->label('Usuários')
                            ->state(fn ($record): string => $record->users()->orderBy('name')->pluck('name')->implode(', ') ?: '-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
