<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\Actions\DeleteUserAction;
use App\Models\Tenant;
use App\Models\User;
use App\Trait\Filament\NotificationsTrait;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    use NotificationsTrait;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('tenants_list')
                    ->label('Tenants')
                    ->state(function (User $record) {
                        $tenantNames = Tenant::query()
                            ->select('name')
                            ->whereIn('id', $record->tenants()->pluck('tenants.id'))
                            ->pluck('name')
                            ->all();

                        return empty($tenantNames) ? '—' : implode(', ', $tenantNames);
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('is_suspended')
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(fn (User $record): string => $record->is_suspended ? __('Suspenso') : __('Autorizado'))
                    ->badge()
                    ->color(fn (User $record): string => $record->is_suspended ? 'danger' : 'primary')
                    ->icon(fn (User $record): string => $record->is_suspended ? 'heroicon-c-no-symbol' : 'heroicon-c-check')
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteUserAction::make()
                    ->visible(
                        fn (User $record): bool => $record->id !== Filament::auth()->id()
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
