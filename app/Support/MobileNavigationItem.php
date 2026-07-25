<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Contracts\Support\Htmlable;

class MobileNavigationItem
{
    public function __construct(
        protected string $label,
        protected string $url,
        protected string|\BackedEnum|Htmlable|null $icon,
        protected string|\BackedEnum|Htmlable|null $activeIcon = null,
        protected bool $isActive = false,
        protected mixed $badge = null,
        protected ?string $badgeColor = null,
        protected int $sort = 0
    ) {}

    public static function make(
        string $label,
        string $url,
        string|\BackedEnum|Htmlable|null $icon,
        string|\BackedEnum|Htmlable|null $activeIcon = null,
        bool $isActive = false,
        mixed $badge = null,
        ?string $badgeColor = null,
        int $sort = 0
    ): self {
        return new self($label, $url, $icon, $activeIcon, $isActive, $badge, $badgeColor, $sort);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return $this->icon;
    }

    public function getActiveIcon(): string|\BackedEnum|Htmlable|null
    {
        return $this->activeIcon;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getBadge(): mixed
    {
        return $this->badge;
    }

    public function getBadgeColor(): ?string
    {
        return $this->badgeColor;
    }

    public function getSort(): int
    {
        return $this->sort;
    }
}
