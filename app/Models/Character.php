<?php

namespace App\Models;

use App\Traits\HasLast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Character extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use HasLast;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'short_description',
        'description',
        'is_published',
        'created_by',
    ];

    const STATUS_PUBLISHED = 1;

    const STATUS_DRAFT = 2;

    const STATUS_REVIEW = 3;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('photo')
            ->singleFile();
    }

    public function sections()
    {
        return $this->morphToMany(
            Section::class,
            'sectionable',
            'sectionables',
            'sectionable_id',
            'section_id'
        );
    }
}
