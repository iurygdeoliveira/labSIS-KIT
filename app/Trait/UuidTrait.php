<?php

declare(strict_types=1);

namespace App\Trait;

use Illuminate\Support\Str;

trait UuidTrait
{
    public static function bootUuidTrait(): void
    {
        static::creating(function ($model): void {
            if (! $model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
