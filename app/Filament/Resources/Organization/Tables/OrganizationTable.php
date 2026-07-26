<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Tables;

use App\Filament\Resources\Organization\Actions\DeleteOrganizationAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('organization.fields.name'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('organization.fields.slug'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('users_count')
                    ->label(__('organization.members.title'))
                    ->counts('users')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('organization.fields.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->tooltip('Visualizar')
                        ->color('gray')
                        ->icon(Heroicon::Eye)
                        ->hiddenLabel(),
                    EditAction::make()
                        ->tooltip('Editar')
                        ->color('success')
                        ->icon(Heroicon::PencilSquare)
                        ->hiddenLabel(),
                    DeleteOrganizationAction::make()
                        ->tooltip('Excluir')
                        ->hiddenLabel(),
                ])
                    ->buttonGroup(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
