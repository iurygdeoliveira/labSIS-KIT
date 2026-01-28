<?php

declare(strict_types=1);

namespace App\Filament\Resources\Authentication\Tables;

use App\Filament\Resources\Authentication\Schemas\AuthenticationLogForm;
use App\Models\AuthenticationLog;
use App\Models\User;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuthenticationLogTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(self::columns())
            ->filters(self::filters())

            ->defaultSort('login_at', 'desc');
    }

    /**
     * Define as colunas da tabela de logs de autenticação.
     *
     * @return array<TextColumn|IconColumn>
     */
    protected static function columns(): array
    {
        return [
            TextColumn::make('authenticatable_id')
                ->label('Usuário')
                ->formatStateUsing(function ($state, AuthenticationLog $record) {
                    // Carrega o usuário diretamente do PostgreSQL usando o ID
                    $user = User::find($record->authenticatable_id);

                    if (! $user) {
                        return 'Desconhecido';
                    }

                    return $user->name;
                })
                ->searchable(isGlobal: false, isIndividual: true),

            TextColumn::make('ip_address')
                ->label('IP')
                ->copyable(),

            TextColumn::make('user_agent')
                ->label('User Agent')
                ->limit(40)
                ->tooltip(fn ($state) => $state),

            TextColumn::make('login_at')
                ->label('Login')
                ->dateTime()
                ->sortable(),

            TextColumn::make('logout_at')
                ->label('Logout')
                ->dateTime()
                ->sortable(),

            IconColumn::make('login_successful')
                ->label('Sucesso')
                ->boolean(),
        ];
    }

    /**
     * Define os filtros da tabela de logs de autenticação.
     *
     * @return array<Filter>
     */
    protected static function filters(): array
    {
        return [
            Filter::make('login_successful')
                ->label('Apenas Sucessos')
                ->query(fn (Builder $query) => $query->where('login_successful', true)),

            Filter::make('login_at')
                ->schema(AuthenticationLogForm::loginAtFilter())
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '<=', $date),
                        );
                }),
        ];
    }
}
