<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'bookmarkable_id',
        'bookmarkable_type',
        'type',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'profile_id' => 'integer',
        'bookmarkable_id' => 'integer',
        'bookmarkable_type' => 'string',
        'type' => 'integer',
    ];

    const TYPE_FAVOURITE = 1;
    const TYPE_WATCHLIST = 2;

    public function bookmarkable()
    {
        return $this->morphTo();
    }
}
