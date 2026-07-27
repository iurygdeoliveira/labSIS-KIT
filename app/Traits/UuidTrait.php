<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method static void creating(\Closure|string|array $callback)
 *
 * @mixin Model
 */
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
}
