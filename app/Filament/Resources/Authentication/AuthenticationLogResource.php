<?php

namespace App\Filament\Resources\Authentication;

use App\Enums\RoleType;
use App\Filament\Resources\Authentication\Pages\ListAuthenticationLogs;
use App\Filament\Resources\Authentication\Schemas\AuthenticationLogForm;
use App\Filament\Resources\Authentication\Tables\AuthenticationLogTable;
use App\Models\AuthenticationLog;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class AuthenticationLogResource extends Resource
{
    protected static ?string $model = AuthenticationLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $navigationLabel = 'Logs de Acesso';

    protected static ?string $modelLabel = 'Log de Acesso';

    protected static ?string $slug = 'authentication-logs';

    protected static ?int $navigationSort = 100;

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return AuthenticationLogForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return AuthenticationLogTable::configure($table);
    }

    #[Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = Filament::auth()->user();
        $query = parent::getEloquentQuery();

        // 1. Admins veem todos os logs
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return $query;
        }

        // 2. Proprietários veem logs dos usuários do seu tenant atual
        $tenant = Filament::getTenant();
        /** @var \App\Models\Tenant|null $tenant */
        if ($tenant && $user->isOwnerOfTenant($tenant)) {
            // Busca IDs de usuários pertencentes a este tenant
            // Nota: Esta é uma query híbrida (SQL -> Mongo). Buscamos os IDs SQL primeiro.
            $tenantUserIds = User::whereHas('tenants', function ($q) use ($tenant) {
                $q->whereKey($tenant->getKey());
            })->pluck('id')->toArray();

            return $query->whereIn('authenticatable_id', $tenantUserIds)
                ->where('authenticatable_type', User::class);
        }

        // 3. Usuários regulares veem apenas seus próprios logs
        return $query->where('authenticatable_id', $user->getAuthIdentifier());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuthenticationLogs::route('/'),
        ];
    }
}
