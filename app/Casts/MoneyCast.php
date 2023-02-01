<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        $value = intval($value);
        $value /= 100;

        return number_format($value, 2, '.', '');
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (blank($value)) {
            return null;
        }

        $value = floatval($value);
        $value *= 100;

        return round($value);
    }
}
