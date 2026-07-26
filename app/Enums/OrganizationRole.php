<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Role;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrganizationRole: string implements HasColor, HasLabel
{
    case Owner = 'Owner';
    case Admin = 'Admin';
    case User = 'User';

    public function getLabel(): string
    {
        return match ($this) {
            self::Owner => __('organization.roles.owner'),
            self::Admin => 'Administrador',
            self::User => __('organization.roles.user'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Owner => 'warning',
            self::Admin => 'info',
            self::User => 'gray',
        };
    }

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom($value)
            ?? self::tryFrom(ucfirst(strtolower($value)))
            ?? self::tryFrom(strtolower($value));
    }

    public function canInviteMembers(): bool
    {
        return match ($this) {
            self::Owner, self::Admin => true,
            default => false,
        };
    }

    public function canManageMembers(): bool
    {
        return match ($this) {
            self::Owner, self::Admin => true,
            default => false,
        };
    }

    public function isProtected(): bool
    {
        return $this === self::Owner;
    }

    public static function ownerValue(): string
    {
        return self::Owner->value;
    }

    public static function orderBySql(string $column): string
    {
        $cases = collect(self::cases())
            ->map(fn (self $role, int $index) => "WHEN '{$role->value}' THEN {$index}")
            ->implode(' ');

        return "CASE {$column} {$cases} ELSE ".count(self::cases()).' END';
    }

    public static function tenantOptions(): array
    {
        return [
            self::Owner->value => self::Owner->getLabel(),
            self::User->value => self::User->getLabel(),
        ];
    }

    public static function assignableOptions(): array
    {
        return collect(self::cases())
            ->reject(fn (self $role) => $role->isProtected())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->getLabel()])
            ->all();
    }

    public static function ensureGlobalRoles(string $guard): void
    {
        // Apenas Admin deve existir de forma global
        Role::firstOrCreate([
            'name' => self::Admin->value,
            'guard_name' => $guard,
        ]);
    }

    public static function ensureOwnerRoleForTeam(int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => self::Owner->value,
            'guard_name' => $guard,
        ]);
    }

    public static function ensureUserRoleForTeam(int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => self::User->value,
            'guard_name' => $guard,
        ]);
    }
}
