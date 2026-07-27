<?php

declare(strict_types=1);

namespace App\Support\NotificationCenter;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

final class NotificationTab
{
    /**
     * @param  string | array<string, string> | null  $color
     */
    public function __construct(
        public readonly string $id,
        public readonly string|Htmlable $label,
        public readonly string|BackedEnum|Htmlable|null $icon,
        public readonly string|array|null $color,
        public readonly int $count,
    ) {}
}
