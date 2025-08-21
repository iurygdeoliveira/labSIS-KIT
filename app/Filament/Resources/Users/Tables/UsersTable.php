<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Trait\Filament\NotificationsTrait;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    use NotificationsTrait;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => $record->getFilamentAvatarUrl())
                    ->extraImgAttributes(['alt' => 'Avatar do usuário']),
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('is_suspended')
                    ->label('Status')
                    ->formatStateUsing(fn (User $record): string => $record->is_suspended ? __('Suspenso') : __('Autorizado'))
                    ->badge()
                    ->color(fn (User $record): string => $record->is_suspended ? 'danger' : 'success')
                    ->icon(fn (User $record): string => $record->is_suspended ? 'heroicon-c-no-symbol' : 'heroicon-c-check')
                    ->alignCenter(),
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
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
