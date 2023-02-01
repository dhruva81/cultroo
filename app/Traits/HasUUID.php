<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUUID
{
    public static function findByUUID($uuid)
    {
        return self::where('uuid', $uuid)->first();
    }

    protected static function bootHasUUID()
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
}
