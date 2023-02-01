<?php

namespace App\Models;

use App\Traits\HasLast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PendingUser extends Model
{
    use HasFactory;
    use Notifiable;
    use HasLast;

    protected $fillable = [
        'name',
        'email',
        'otp',
        'user_type',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->otp = mt_rand(1000, 9999);
        });
    }
}
