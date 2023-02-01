<?php

namespace App\Traits;

trait HasLast
{
    public static function last(): self
    {
        return self::latest()->first();
    }
}
