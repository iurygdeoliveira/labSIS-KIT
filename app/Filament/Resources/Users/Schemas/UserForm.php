<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(fn (): array => Tenant::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->dehydrated(false)
                    ->visible(function (string $operation): bool {
                        if ($operation !== 'create') {
                            return false;
                        }

                        $user = Filament::auth()->user();
                        if (! ($user instanceof User) || ! method_exists($user, 'hasRole')) {
                            return false;
                        }

                        return (bool) $user->hasRole(RoleType::ADMIN->value);
                    })
                    ->required(function (string $operation): bool {
                        if ($operation !== 'create') {
                            return false;
                        }

                        $user = Filament::auth()->user();
                        if (! ($user instanceof User) || ! method_exists($user, 'hasRole')) {
                            return false;
                        }

                        return (bool) $user->hasRole(RoleType::ADMIN->value);
                    }),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->requiredWith('password')
                    ->dehydrated(false),
                Toggle::make('is_suspended')
                    ->label(fn (Get $get): string => $get('is_suspended') ? 'Usuário não autorizado' : 'Usuário autorizado')
                    ->onColor('danger')
                    ->offColor('primary')
                    ->onIcon('heroicon-c-no-symbol')
                    ->offIcon('heroicon-c-check')
                    ->default(fn ($record): bool => (bool) ($record?->is_suspended))
                    ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                    ->hint(fn (?User $record): ?string => $record?->getKey() === Auth::id() ? __('Você não pode suspender a si mesmo.') : null)
                    ->hintColor('danger')
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('suspension_reason')
                    ->label('Motivo da suspensão')
                    ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
