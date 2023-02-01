<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    use HasFactory;

    protected $fillable = [
        'avatar_category',
        'avatar_path',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function getAvatarCategoryAttribute($value): ?string
    {
        return ucfirst($value);
    }

    public function getAvatar(): ?string
    {
        if($this->avatar_path && Storage::disk('s3')->exists($this->avatar_path)) {
            return Storage::disk('s3')->url($this->avatar_path);
        }

        return null;
    }

    protected static function booted()
    {

        static::updated(function ($model) {
            if ($model->isDirty('avatar_path')) {
                // Delete the old image from S3
                Storage::disk('s3')->delete($model->getOriginal('avatar_path'));
            }
        });

    }
}
