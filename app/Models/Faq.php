<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'support_category_id',
        'is_published',
    ];

    public function supportCategory(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class);
    }
}
