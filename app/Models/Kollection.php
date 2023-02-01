<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Kollection extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasUUID;

    protected $table = 'collections';

    protected $fillable = [
        'name',
        'color',
        'description',
        'status',
        'uuid',
        'created_by',
        'deleted_by',
    ];

    const STATUS_PUBLISHED = 1;

    const STATUS_DRAFT = 2;

    const STATUS_REVIEW = 3;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
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

    // Series
    public function series()
    {
        return $this->morphedByMany(
            Series::class,
            'collectable',
            'collectables',
            'collection_id',
            'collectable_id'
        );
    }

    public function getStreamableSeries()
    {
        return $this->series()
            ->published()
            ->has('publishedAndStreamableVideos', '>', 0)
            ->get();
    }

    // Videos
    public function videos()
    {
        return $this->morphedByMany(
            Video::class,
            'collectable',
            'collectables',
            'collection_id',
            'collectable_id'
        );
    }

    public function shorts()
    {
        return $this
            ->videos()
            ->published()
            ->streamable()
            ->whereNull('series_id');
    }

    public function getStreamableShorts()
    {
        return $this
            ->videos()
            ->published()
            ->streamable()
            ->whereNull('series_id')
            ->get();
    }
}
