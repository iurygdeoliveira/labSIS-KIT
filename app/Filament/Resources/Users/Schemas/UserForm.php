<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RoleType;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Team')
                    ->description('Selecione o team ao qual o usuário pertence')
                    ->columnSpanFull()
                    ->visible(fn (string $operation): bool => self::shouldShowTenantField($operation))
                    ->components(self::getTenantFields()),

                Section::make('Dados Pessoais')
                    ->description('Informações básicas e controle de acesso do usuário')
                    ->columnSpanFull()
                    ->components(self::getPersonalDataFields()),

                Section::make('Autenticação')
                    ->description('Configure a senha de acesso do usuário')
                    ->columnSpanFull()
                    ->components(self::getAuthenticationFields()),
            ]);
    }

    private static function getTenantFields(): array
    {
        return [
            Select::make('tenant_id')
                ->label('Team')
                ->options(fn (): array => Team::query()
                    ->where('is_active', true)
                    ->orderBy('name', 'asc')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->native(false)
                ->dehydrated(false)
                ->required()
                ->columnSpanFull(),
        ];
    }

    private static function getPersonalDataFields(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome Completo')
                ->required(),

            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required(),

            Toggle::make('is_suspended')
                ->label(fn (Get $get): string => $get('is_suspended') ? 'Usuário suspenso' : 'Usuário com acesso liberado')
                ->onColor('danger')
                ->offColor('primary')
                ->onIcon(Heroicon::NoSymbol)
                ->offIcon(Heroicon::Check)
                ->default(fn ($record): bool => (bool) ($record?->is_suspended))
                ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                ->hint(fn (?User $record): ?string => $record?->getKey() === Auth::id() ? __('Você não pode suspender a si mesmo.') : null)
                ->hintColor('danger')
                ->hidden(fn (string $operation): bool => $operation === 'create')
                ->columnSpanFull(),

            TextInput::make('suspension_reason')
                ->label('Motivo da Suspensão')
                ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                ->placeholder('Descreva o motivo da suspensão...')
                ->hidden(fn (string $operation): bool => $operation === 'create')
                ->columnSpanFull(),
        ];
    }

    private static function getAuthenticationFields(): array
    {
        return [
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->revealable()
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->confirmed(),

            TextInput::make('password_confirmation')
                ->label('Confirmar Senha')
                ->password()
                ->revealable()
                ->requiredWith('password')
                ->dehydrated(false),
        ];
    }

    private static function shouldShowTenantField(string $operation): bool
    {
        if ($operation !== 'create') {
            return false;
        }

        $user = Filament::auth()->user();
        if (! ($user instanceof User)) {
            return false;
        }

        return $user->hasRole(RoleType::ADMIN->value);
    }
}
