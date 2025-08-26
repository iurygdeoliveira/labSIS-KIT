<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Permission: string implements HasLabel
{
    case DELETE = 'delete';
    case CREATE = 'create';
    case UPDATE = 'update';
    case VIEW = 'view';

    public function getLabel(): string
    {
        return match ($this) {
            self::DELETE => 'Apagar',
            self::CREATE => 'Criar',
            self::UPDATE => 'Editar',
            self::VIEW => 'Visualizar',
        };
    }
}
