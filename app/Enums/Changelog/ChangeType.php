<?php

declare(strict_types=1);

namespace App\Enums\Changelog;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ChangeType: string implements HasColor, HasIcon, HasLabel
{
    case Added = 'added';
    case Changed = 'changed';
    case Deprecated = 'deprecated';
    case Removed = 'removed';
    case Fixed = 'fixed';
    case Security = 'security';

    public function getLabel(): string
    {
        return match ($this) {
            self::Added => 'Adicionado',
            self::Changed => 'Modificado',
            self::Deprecated => 'Descontinuado',
            self::Removed => 'Removido',
            self::Fixed => 'Corrigido',
            self::Security => 'Segurança',
        };
    }

    public function getCanonicalLabel(): string
    {
        return match ($this) {
            self::Added => 'Added',
            self::Changed => 'Changed',
            self::Deprecated => 'Deprecated',
            self::Removed => 'Removed',
            self::Fixed => 'Fixed',
            self::Security => 'Security',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Added => 'success',
            self::Changed => 'info',
            self::Deprecated => 'warning',
            self::Removed => 'danger',
            self::Fixed => 'primary',
            self::Security => 'danger',
        };
    }

    public function getIcon(): \BackedEnum|string|null
    {
        return match ($this) {
            self::Added => Heroicon::OutlinedPlusCircle,
            self::Changed => Heroicon::OutlinedArrowPath,
            self::Deprecated => Heroicon::OutlinedExclamationTriangle,
            self::Removed => Heroicon::OutlinedMinusCircle,
            self::Fixed => Heroicon::OutlinedWrench,
            self::Security => Heroicon::OutlinedShieldExclamation,
        };
    }

    public static function fromHeading(string $heading): ?self
    {
        $normalized = strtolower(trim($heading));

        return match ($normalized) {
            'added', 'adicionado', 'novidades', 'adicionados' => self::Added,
            'changed', 'modificado', 'alterado', 'alterações', 'modificados', 'alterados' => self::Changed,
            'deprecated', 'descontinuado', 'obsoleto', 'descontinuados' => self::Deprecated,
            'removed', 'removido', 'excluído', 'removidos' => self::Removed,
            'fixed', 'corrigido', 'correção', 'correções', 'corrigidos' => self::Fixed,
            'security', 'segurança' => self::Security,
            default => self::tryFrom($normalized),
        };
    }
}
