<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
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
                    ->live()
                    ->onColor('danger')
                    ->offColor('success')
                    ->onIcon('heroicon-c-no-symbol')
                    ->offIcon('heroicon-c-check')
                    ->default(fn ($record): bool => (bool) ($record?->is_suspended))
                    ->afterStateUpdated(function (bool $state, callable $set): void {
                        $set('suspended_at', $state ? now() : null);
                    })
                    ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                    ->hint(fn (?User $record): ?string => $record?->getKey() === Auth::id() ? __('Você não pode suspender a si mesmo.') : null)
                    // Persistir diretamente em is_suspended no salvamento
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                DateTimePicker::make('suspended_at')
                    ->label('Suspenso em')
                    ->seconds(false)
                    ->readOnly()
                    ->dehydrated(true)
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                Textarea::make('suspension_reason')
                    ->label('Motivo da suspensão')
                    ->rows(3)
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
