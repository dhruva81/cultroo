<?php

namespace App\Models;

use App\Traits\HasLast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Language extends Model
{
    use HasFactory;
    use HasSlug;
    use HasLast;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'status' => 'integer',
    ];

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 2;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function getStreamableShorts(): Collection
    {
        return $this
            ->videos()
            ->published()
            ->streamable()
            ->whereNull('series_id')
            ->get();
    }

    public function getStreamableSeries(): Collection|array
    {
        return Series::with('publishedAndStreamableVideos')
            ->get()
            ->filter(function($series){
                return $series
                    ->getLanguages()
                    ->pluck('id')
                    ->contains(1);
            });
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
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
