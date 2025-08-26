<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoleType: string implements HasLabel
{
    case ADMIN = 'Admin';
    case USER = 'User';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::USER => 'Usuário',
        };
    }
}
