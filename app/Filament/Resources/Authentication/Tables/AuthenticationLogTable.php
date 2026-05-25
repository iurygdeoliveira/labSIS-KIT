<?php

declare(strict_types=1);

namespace App\Filament\Resources\Authentication\Tables;

use App\Filament\Resources\Authentication\Schemas\AuthenticationLogForm;
use App\Models\AuthenticationLog;
use App\Models\User;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\TernaryFilter;
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

    /** @var array<string, string> Cache por requisição: evita N+1 e não usa morphTo SQL↔Mongo. */
    private static array $userDisplayNameCache = [];

    private static function userDisplayNameForLog(AuthenticationLog $record): string
    {
        $type = (string) ($record->authenticatable_type ?? '');
        $id = $record->authenticatable_id;
        if ($type !== User::class || $id === null || $id === '') {
            return 'Desconhecido';
        }

        $key = $type.':'.$id;
        if (! array_key_exists($key, self::$userDisplayNameCache)) {
            self::$userDisplayNameCache[$key] = User::query()->find($id)?->name ?? 'Desconhecido';
        }

        return self::$userDisplayNameCache[$key];
    }

    /**
     * Define as colunas da tabela de logs de autenticação.
     *
     * @return array<TextColumn|IconColumn>
     */
    protected static function columns(): array
    {
        return [
            TextColumn::make('user_display')
                ->label('Usuário')
                ->getStateUsing(fn (AuthenticationLog $record): string => self::userDisplayNameForLog($record))
                ->searchable(isGlobal: false, isIndividual: true, query: function (Builder $query, string $search): Builder {
                    $ids = User::query()
                        ->where(function (Builder $q) use ($search): void {
                            $like = '%'.addcslashes($search, '%_\\').'%';
                            $q->where('name', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        })
                        ->pluck('id')
                        ->all();

                    return $query->whereIn('authenticatable_id', $ids)
                        ->where('authenticatable_type', User::class);
                }),

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
     * @return array<Filter|TernaryFilter>
     */
    protected static function filters(): array
    {
        return [
            TernaryFilter::make('login_successful')
                ->label('Resultado do login')
                ->placeholder('Todos')
                ->trueLabel('Somente sucessos')
                ->falseLabel('Somente falhas'),

            Filter::make('login_at')
                ->schema(AuthenticationLogForm::loginAtFilter())
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['from'] ?? null) {
                        $indicators[] = Indicator::make('Login de: '.$data['from'])->removeField('from');
                    }
                    if ($data['until'] ?? null) {
                        $indicators[] = Indicator::make('Login até: '.$data['until'])->removeField('until');
                    }

                    return $indicators;
                })
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
