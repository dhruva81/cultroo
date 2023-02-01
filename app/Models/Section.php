<?php

namespace App\Models;

use App\Http\Resources\CharacterResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\SeriesResource;
use App\Http\Resources\VideoResource;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Section extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasUUID;

    protected $fillable = [
        'name',
        'model',
        'status',
        'layout',
        'color',
        'created_by',
        'deleted_by',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;
    const STATUS_REVIEW = 3;

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

    // Items
    public function getItems()
    {
        if($this->model === 'series')
        {
            return SeriesResource::collection($this->getStreamableSeries());
        }

        if($this->model === 'video')
        {
            return VideoResource::collection($this->getStreamableShorts());
        }

        if($this->model === 'language')
        {
            return LanguageResource::collection($this->languages);
        }

        if($this->model === 'character')
        {
            return CharacterResource::collection($this->characters);
        }

        return [];
    }

    // Series
    public function series()
    {
        return $this->morphedByMany(
            Series::class,
            'sectionable',
            'sectionables',
            'section_id',
            'sectionable_id'
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
            'sectionable',
            'sectionables',
            'section_id',
            'sectionable_id'
        );
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

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    // Booted
    protected static function booted()
    {
        static::updated(function ($model) {

            if($model->isDirty('model'))
            {
                $model->series()->detach();
                $model->videos()->detach();
            }

        });
    }

    public function getAddToSectionLink(): string
    {
        if($this->model === 'series')
        {
           return route('series.datatable', ['section' => $this]);
        }

        if($this->model === 'video')
        {
            return route('shorts.datatable', ['section' => $this]);
        }

        if($this->model === 'character')
        {
            return route('characters.datatable', ['section' => $this]);
        }

        if($this->model === 'language')
        {
            return route('languages.datatable', ['section' => $this]);
        }

        return '#';
    }

    // Characters
    public function characters()
    {
        return $this->morphedByMany(
            Series::class,
            'sectionable',
            'sectionables',
            'section_id',
            'sectionable_id'
        );
    }

    // Languages
    public function languages()
    {
        return $this->morphedByMany(
            Series::class,
            'sectionable',
            'sectionables',
            'section_id',
            'sectionable_id'
        );
    }
}
