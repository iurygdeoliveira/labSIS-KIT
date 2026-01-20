<?php

namespace App\Filament\Resources\Authentication\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthenticationLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detalhes do Acesso')
                ->schema([
                    TextInput::make('ip_address')
                        ->label('Endereço IP'),

                    Textarea::make('user_agent')
                        ->label('User Agent')
                        ->columnSpanFull(),

                    DateTimePicker::make('login_at')
                        ->label('Data de Login'),

                    DateTimePicker::make('logout_at')
                        ->label('Data de Logout'),

                    Toggle::make('login_successful')
                        ->label('Login com Sucesso')
                        ->disabled(),
                ])->columns(2),
        ]);
    }

    public static function loginAtFilter(): array
    {
        return [
            DatePicker::make('from')->label('De'),
            DatePicker::make('until')->label('Até'),
        ];
    }
}
