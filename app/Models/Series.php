<?php

namespace App\Models;

use App\Enums\TranscodingStatus;
use App\Traits\HasBookmarks;
use App\Traits\HasLast;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\LaravelAdjacencyList\Eloquent\Collection as AdjacencyListCollection;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Series extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasUUID;
    use InteractsWithMedia;
    use HasSlug;
    use HasLast;
    use HasTags;
    use HasRelationships;
    use HasRecursiveRelationships;
    use HasBookmarks;

    protected $fillable = [
        'title',
        'description',
        'synopsis',
        'status',
        'parent_id',
        'season_number',
        'min_age',
        'max_age',
        'created_by',
        'deleted_by',
        'uuid',
        'color',
        'is_featured'
    ];

    protected $casts = [
        'title' => 'string',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'is_featured' => 'boolean',
    ];

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;
    const STATUS_REVIEW = 3;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Videos in this series
    public function videos(): HasMany
    {
        return $this
            ->hasMany(Video::class);
    }

    public function publishedAndStreamableVideos(): HasMany
    {
        return $this->videos()
            ->whereNotNull('streamable_video_path')
            ->where('status', Video::STATUS_PUBLISHED);
    }

    public function getDefaultLanguage()
    {
        return $this->getLanguagesOfPublishedAndStreamableVideos()?->first();
    }

    public function getEpisodesByLanguage($languageId = null)
    {
        if(! $languageId) {
            $languageId = $this->getDefaultLanguage()?->id;
        }

        return $this->publishedAndStreamableVideos()
            ->where('language_id', $languageId)
            ->orderBy('episode_number')
            ->get();
    }

    public function getEpisodeNumbers(): array
    {
        return $this->videos
            ->unique('episode_number')
            ->sortBy('episode_number')
            ->pluck('episode_number')
            ->mapWithKeys(fn($n) => [$n => 'Episode '. $n])
            ->toArray();
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getBadgeStatusAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 'draft';
            case self::STATUS_REVIEW:
                return 'review';
            case self::STATUS_PUBLISHED && $this->publishedAndStreamableVideos()->count() > 0:
                return 'published';
            case self::STATUS_PUBLISHED && $this->publishedAndStreamableVideos()->count() === 0:
                return 'published_not_visible';
        }
        return 'draft';
    }

//    public function genres(): HasManyDeep
//    {
//        return $this->hasManyDeep(
//            Genre::class,
//            [Video::class, 'genre_video'],
//        )->distinct();
//    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany( Genre::class, 'genreable');
    }

    public function characters(): HasManyDeep
    {
        return $this->hasManyDeep(
            Character::class,
            [Video::class, 'character_video'],
        )->distinct();
    }

    /*
    getLanguages() will fetch list of languages of videos in this series.
    It will return a collection of languages.
    */

    public function getLanguages()
    {
        return $this
            ->videos
            ->pluck('language')
            ->unique()
            ->filter()
            ->sortBy('id'); // This will reject null values. You can also use ->reject(null)
    }

    public function getLanguagesOfPublishedAndStreamableVideos()
    {
        return $this
            ->publishedAndStreamableVideos
            ->pluck('language')
            ->unique()
            ->filter()
            ->sortBy('id'); // This will reject null values. You can also use ->reject(null)
    }

    public function scopeFirstSeason($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getSeasons(): AdjacencyListCollection
    {
        if (! $this->parent_id) {
            return $this
                ->descendantsAndSelf
                ->sortBy('season_number');
        }

        return $this
            ->rootAncestor
            ->childrenAndSelf
            ->sortBy('season_number');
    }

    public function getPublishedSeasons(): AdjacencyListCollection
    {
        return $this
             ->getSeasons()
             ->where('status', self::STATUS_PUBLISHED)
             ->filter(function ($query) {
                 return $query->publishedAndStreamableVideos->count() > 0;
             });
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('video')
            ->singleFile();

        $this
            ->addMediaCollection('thumbnail')
            ->useFallbackUrl(asset('assets/images/image-not-available.png'))
            ->singleFile();
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if(!$model->min_age) $model->min_age = 1;

            if ($model->parent_id) {
                $previousSeasonNumber = Series::find($model->parent_id)
                    ->children
                    ->pluck('season_number')
                    ->max();
                $model->season_number = $previousSeasonNumber + 1;
            }
        });

        static::updating(function ($model) {
            if(!$model->min_age) $model->min_age = 1;
        });
    }

    public function getRelatedSeries(): \Illuminate\Support\Collection
    {
        return $this
            ->genres()
            ->get()
            ->map(fn ($genre) => $genre->series)
            ->flatten()
            ->unique()
            ->filter(fn ($series) => $series->id !== $this->id)
            ->shuffle()
            ->take(6);
    }

    public function getRelatedShorts(): \Illuminate\Support\Collection
    {
        return $this
            ->genres()
            ->get()
            ->map(fn ($genre) => $genre->videos)
            ->flatten()
            ->unique()
            ->filter(fn($video) => ! $video->series_id)
            ->where('status', Video::STATUS_PUBLISHED)
            ->whereNotNull('streamable_video_path')
            ->shuffle()
            ->take(6);
    }

    public function collections()
    {
        return $this->morphToMany(
            Kollection::class,
            'collectable',
            'collectables',
            'collectable_id',
            'collection_id'
        );
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
