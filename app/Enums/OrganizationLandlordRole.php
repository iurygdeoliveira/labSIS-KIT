<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrganizationLandlordRole: string implements HasLabel
{
    case Admin = 'admin';
    case User = 'user';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => __('organization.roles.admin'),
            self::User => __('organization.roles.user'),
        };
    }
}
