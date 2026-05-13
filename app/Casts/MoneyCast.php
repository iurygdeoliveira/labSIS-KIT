<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): float
    {
        // Transforma o inteiro armazenado no banco de dados em um float.
        return round(floatval($value) / 100, precision: 2);
    }

    public function set($model, string $key, $value, array $attributes): float
    {
        // Transforma o float em um inteiro para armazenamento.
        return round(floatval($value) * 100);
    }
}
