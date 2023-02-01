<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_published',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }
}
