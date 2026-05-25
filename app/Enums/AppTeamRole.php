<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;

/**
 * Papel do usuário dentro de um Team (espelho de UI).
 *
 * Mapeamento para Spatie {@see RoleType}:
 *   - OWNER  ↔ RoleType::OWNER ('Owner')
 *   - MEMBER ↔ RoleType::USER ('User')
 *
 * A autorização real continua via Spatie. Este enum só alimenta o pivot
 * `team_members.role` e a UI do FilaTeams (badges, dropdowns, convites).
 */
enum AppTeamRole: string implements TeamRoleContract
{
    case OWNER = 'owner';
    case MEMBER = 'member';

    public static function owner(): static
    {
        return self::OWNER;
    }

    public static function default(): static
    {
        return self::MEMBER;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        return collect(self::cases())
            ->reject(fn (self $role): bool => $role === self::OWNER)
            ->map(fn (self $role): array => [
                'value' => $role->value,
                'label' => (string) $role->getLabel(),
            ])
            ->values()
            ->toArray();
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::OWNER => RoleType::OWNER->getLabel(),
            self::MEMBER => RoleType::USER->getLabel(),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OWNER => 'danger',
            self::MEMBER => 'info',
        };
    }

    /**
     * @return array<int, string|AppTeamPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::OWNER => AppTeamPermission::cases(),
            self::MEMBER => [],
        };
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, array_map(
            static fn (mixed $p): string => $p instanceof BackedEnum ? $p->value : (string) $p,
            $this->permissions()
        ), strict: true);
    }

    public function level(): int
    {
        return match ($this) {
            self::OWNER => 3,
            self::MEMBER => 1,
        };
    }

    public function isAtLeast(TeamRoleContract $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * Mapeia este papel de pivot para o nome da role Spatie correspondente.
     */
    public function toSpatieRoleName(): string
    {
        return match ($this) {
            self::OWNER => RoleType::OWNER->value,
            self::MEMBER => RoleType::USER->value,
        };
    }
}
