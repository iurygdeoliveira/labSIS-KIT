<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Filament\Resources\Tenants\Actions\DeleteTenantAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('is_active')
                    ->label('Ativo')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sim' : 'Não')
                    ->badge()
                    ->color(fn ($record): string => (bool) $record->is_active ? 'primary' : 'danger'),
                TextColumn::make('users_count')->counts('users')->label('Usuários'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteTenantAction::make(),
            ])
            ->headerActions([

            ]);
    }
}
