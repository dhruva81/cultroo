<?php

namespace App\Models;

use App\Traits\HasLast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Genre extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    use HasLast;
    use InteractsWithMedia;
    use HasRelationships;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'status',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'name' => 'string',
        'status' => 'integer',
    ];

    const STATUS_PUBLISHED = 1;

    const STATUS_DRAFT = 2;

    const STATUS_REVIEW = 3;

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


    // SERIES
    public function series(): MorphToMany
    {
        return $this->morphedByMany( Series::class, 'genreable');
    }

    public function streamableSeries(): MorphToMany
    {
        return $this->series()
            ->published()
            ->has('publishedAndStreamableVideos', '>', 0);
    }

    // Videos
    public function videos(): MorphToMany
    {
        return $this->morphedByMany( Video::class, 'genreable');
    }

    public function streamableShorts()
    {
        return $this
            ->videos()
            ->published()
            ->streamable()
            ->whereNull('series_id');
    }

    // Profiles
    public function profiles(): MorphToMany
    {
        return $this->morphedByMany( Profile::class, 'genreable');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('icon')
            ->useFallbackUrl(asset('assets/images/image-not-available.png'))
            ->singleFile();

        $this
            ->addMediaCollection('cover_image')
            ->useFallbackUrl(asset('assets/images/image-not-available.png'))
            ->singleFile();
    }
}
