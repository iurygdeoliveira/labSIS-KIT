<?php

namespace App\Filament\Resources\Teams\Tables;

use App\Filament\Resources\Teams\Actions\DeleteTeamAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsTable
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
                TextColumn::make('members.name')
                    ->label('Usuários')
                    ->listWithLineBreaks()
                    ->bulleted(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->iconButton()->icon(Heroicon::Eye)->tooltip('Visualizar'),
                EditAction::make()->iconButton()->icon(Heroicon::Pencil)->tooltip('Editar'),
                DeleteTeamAction::make()->iconButton()->icon(Heroicon::Trash)->tooltip('Excluir'),
            ])
            ->headerActions([

            ])
            ->defaultSort('name', 'desc');
    }
}
