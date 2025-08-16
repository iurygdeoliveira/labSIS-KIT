<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case IDEATION = 'Ideation';
    case PROTOTYPING = 'Prototyping';
    case IN_PRODUCTION = 'In production';
    case TESTING = 'Testing';
    case REGISTRATION = 'Registration';
    case APPROVAL = 'Approval';

    public function getLabel(): string
    {
        return match ($this) {
            self::IDEATION => 'Ideação',
            self::PROTOTYPING => 'Prototipagem',
            self::IN_PRODUCTION => 'Em produção',
            self::TESTING => 'Testes',
            self::REGISTRATION => 'Registrado',
            self::APPROVAL => 'Em aprovação',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::IDEATION => 'icon-idea',
            self::PROTOTYPING => 'icon-cube',
            self::IN_PRODUCTION => 'icon-settings-cog',
            self::TESTING => 'icon-a-b',
            self::REGISTRATION => 'icon-certificate',
            self::APPROVAL => 'icon-zoom-exclamation',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::IDEATION => 'primary',
            self::PROTOTYPING => 'light',
            self::IN_PRODUCTION => 'secondary',
            self::TESTING => 'info',
            self::REGISTRATION => 'success',
            self::APPROVAL => 'warning'
        };
    }
}
