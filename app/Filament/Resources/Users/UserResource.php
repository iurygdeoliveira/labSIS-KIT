<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\DeleteUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class UserResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = User::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organizations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?string $title = 'Usuários';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getModelLabel(): string
    {
        return __('User');
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['organizations', 'rolesWithTeams']);
    }

    #[Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'uuid';
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
            'delete' => DeleteUser::route('/{record}/delete'),
        ];
    }
}
