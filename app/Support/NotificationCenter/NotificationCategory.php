<?php

declare(strict_types=1);

namespace App\Support\NotificationCenter;

use BackedEnum;
use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class NotificationCategory
{
    use EvaluatesClosures;

    protected string|Htmlable|Closure|null $label = null;

    protected string|BackedEnum|Htmlable|Closure|null $icon = null;

    protected string|array|Closure|null $color = null;

    protected int|Closure $order = 0;

    final public function __construct(
        protected readonly string $id,
    ) {}

    public static function make(string $id): static
    {
        return new static($id);
    }

    /**
     * Build a category from a BackedEnum case, reading its label, icon, and color
     * from Filament's HasLabel/HasIcon/HasColor contracts when implemented.
     */
    public static function fromEnum(BackedEnum $case): static
    {
        $category = static::make((string) $case->value);

        if ($case instanceof HasLabel) {
            $category->label($case->getLabel());
        }

        if ($case instanceof HasIcon) {
            $category->icon($case->getIcon());
        }

        if ($case instanceof HasColor) {
            $category->color($case->getColor());
        }

        return $category;
    }

    public function label(string|Htmlable|Closure|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string|BackedEnum|Htmlable|Closure|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string|array|Closure|null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function order(int|Closure $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string|Htmlable
    {
        return $this->evaluate($this->label) ?? Str::headline($this->id);
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return $this->evaluate($this->icon);
    }

    /**
     * @return string | array<string, string> | null
     */
    public function getColor(): string|array|null
    {
        return $this->evaluate($this->color);
    }

    public function getOrder(): int
    {
        return $this->evaluate($this->order);
    }
}
