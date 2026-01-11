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

    #[\Override]
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Implementação de filtragem manual por tenant
        // O Filament normalmente aplica scope automático de tenant, mas como o modelo
        // AuthenticationLog não tem relacionamento direto com Tenant, precisamos
        // filtrar manualmente através do relacionamento polimórfico 'authenticatable'
        // que aponta para o modelo User, que por sua vez tem relacionamento com Tenant.
        //
        // Por isso desabilitamos o scope automático (isScopedToTenant = false) e
        // implementamos a filtragem manualmente neste método.
        $user = \Filament\Facades\Filament::auth()->user();

        if ($user && $user->hasRole(\App\Enums\RoleType::ADMIN->value)) {
            return $query->with('authenticatable');
        }

        return $query->with('authenticatable')->whereHasMorph('authenticatable', [\App\Models\User::class], function (\Illuminate\Contracts\Database\Query\Builder $q): void {
            if ($tenant = \Filament\Facades\Filament::getTenant()) {
                $q->whereHas('tenants', fn (\Illuminate\Contracts\Database\Query\Builder $t) => $t->whereKey($tenant->getKey()));
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
