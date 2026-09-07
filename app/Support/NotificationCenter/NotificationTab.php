<?php

declare(strict_types=1);

namespace App\Support\NotificationCenter;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

final readonly class NotificationTab
{
    /**
     * @param  string | array<string, string> | null  $color
     */
    public function __construct(
        public string $id,
        public string|Htmlable $label,
        public string|BackedEnum|Htmlable|null $icon,
        public string|array|null $color,
        public int $count,
    ) {}
}
