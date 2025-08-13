<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('email_verified_at')
                    ->label('Email Verificado')
                    ->dateTime('d-m-Y H:i'),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d-m-Y H:i'),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d-m-Y H:i'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
