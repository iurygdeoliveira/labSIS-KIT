<?php

namespace App\Filament\Resources\Authentication;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Tapp\FilamentAuthenticationLog\Resources\AuthenticationLogResource as BaseAuthenticationLogResource;

class CustomAuthenticationLogResource extends BaseAuthenticationLogResource
{
    protected static ?string $slug = 'authentication-logs';

    // Desabilita o scope automático de tenant do Filament
    // porque fazemos a filtragem manualmente no getEloquentQuery()
    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Remove Filament's default tenancy scope if it was applied (though overriding usually avoids it if we build query manually, but parent calls model->query())
        // Actually, Filament applies tenancy scope via Global Scope or in the query builder?
        // In Filament v3, checking source, tenancy is applied in `getEloquentQuery`?
        // No, usually it's applied via `Scope` classes.
        // But since we can't easily remove a scope we don't know the exact name of...
        // Let's rely on the fact that the Model does NOT have the global scope by default.
        // The Resource applies it.

        // Wait, if parent::getEloquentQuery() calls Resource::getEloquentQuery(), dealing with tenancy?
        // Resource::getEloquentQuery() -> $model::query().
        // Then Filament adds scopes.

        // Let's implement manual scoping and HOPE we can suppress the error.
        // The error "RelationNotFound" comes from WHERE check.

        // Better strategy:
        // Filter manually.
        $user = \Filament\Facades\Filament::auth()->user();

        if ($user && $user->hasRole(\App\Enums\RoleType::ADMIN->value)) {
            return $query->with('authenticatable');
        }

        return $query->with('authenticatable')->whereHasMorph('authenticatable', [\App\Models\User::class], function ($q) {
            if ($tenant = \Filament\Facades\Filament::getTenant()) {
                $q->whereHas('tenants', fn ($t) => $t->whereKey($tenant->getKey()));
            }
        });
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort(
                config('filament-authentication-log.sort.column'),
                config('filament-authentication-log.sort.direction'),
            )
            ->columns([
                TextColumn::make('authenticatable')
                    ->label(trans('filament-authentication-log::filament-authentication-log.column.authenticatable'))
                    ->formatStateUsing(fn (?string $state, Model $record) => $record->authenticatable->name ?? new HtmlString('&mdash;'))
                    ->sortable(['authenticatable_id']),
                TextColumn::make('ip_address')
                    ->label(trans('filament-authentication-log::filament-authentication-log.column.ip_address'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_agent')
                    ->label(trans('filament-authentication-log::filament-authentication-log.column.user_agent'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                TextColumn::make('login_at')
                    ->label(trans('filament-authentication-log::filament-authentication-log.column.login_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('logout_at')
                    ->label(trans('filament-authentication-log::filament-authentication-log.column.logout_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                //
            ])
            ->filters([
                Filter::make('login_successful')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('login_successful', true)),
                Filter::make('login_at')
                    ->schema([
                        DatePicker::make('login_from'),
                        DatePicker::make('login_until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['login_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '>=', $date),
                        )
                        ->when(
                            $data['login_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '<=', $date),
                        )),
                Filter::make('cleared_by_user')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('cleared_by_user', true)),
            ]);
    }
}
