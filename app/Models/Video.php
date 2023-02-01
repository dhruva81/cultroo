<?php

namespace App\Models;

use App\Enums\TranscodingStatus;
use App\Jobs\StartAWSTranscodingVideoJob;
use App\Traits\HasBookmarks;
use App\Traits\HasLast;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Spatie\MediaLibrary\MediaCollections\File;

class Video extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasUUID;
    use HasLast;
    use InteractsWithMedia;
    use HasTags;
    use HasBookmarks;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $with = ['language'];

    protected $fillable = [
        'title',
        'description',
        'is_free',
        'status',
        'language_id',
        'series_id',
        'episode_number',
        'watch_count',
        'created_by',
        'deleted_by',
        'released_at',
        'min_age',
        'max_age',
        'run_time',
        'uploaded_video_path',
        'streamable_video_path',
        'uploaded_video_meta',
        'streamable_video_meta',
        'file_name',
        'transcoding_status',
        'is_featured'
    ];

    protected $casts = [
        'title' => 'string',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'uploaded_video_meta' => 'json',
        'streamable_video_meta' => 'json',
        'transcoding_status' => 'integer',
        'status' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
    ];

    protected $dates = ['released_at'];

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;
    const STATUS_REVIEW = 3;

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'transcoding_status' => 1,  // Be careful to use the TranscodingStatus enum here
    ];

    public static function getVideoStatus($status): string
    {
        return [
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_REVIEW => 'Review',
        ][$status];
    }

    public function getTranscodingStatus(): string|null
    {
        return TranscodingStatus::tryFrom($this->transcoding_status)?->getLabel();
    }

    public function getAvailableFormats(): ?string
    {
        return
            (isset($this->streamable_video_meta)
                && !empty($this->streamable_video_meta)
                && isset($this->streamable_video_meta[0])
                && isset($this->streamable_video_meta[0]['outputDetails']))
                ? collect($this->streamable_video_meta[0]['outputDetails'])
                ->pluck('videoDetails.heightInPx')
                ->map(fn($h) => $h . 'p')
                ->implode(', ')
                : null;
    }

    public function canWatchVideo(): bool
    {
        if ($this->is_free || auth()->user()->isActiveSubscriber()) {
            return true;
        }

        return false;
    }

    public function getVideoUrForAdmin(): string|null
    {
        if (!empty($this->uploaded_video_path)) {
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$this->uploaded_video_path}";
        }

        return null;
    }

    public function getVideoUrl(): string|null
    {
        if ($this->canWatchVideo()) {
            return $this->getFirstMediaUrl('video');
        }

        return null;
    }

    public function getStreamableVideoUrl(): string|null
    {
        if ($this->canWatchVideo() && filled($this->streamable_video_path)) {
            return config('app.cloudfront_url') . '/' . $this->streamable_video_path;
        }

        return null;
    }

    public function getFallBackUrl(): ?string
    {
        if ($this->canWatchVideo()) {
            return null;
        }

        return 'https://praineo.s3.ap-south-1.amazonaws.com/assets/subscribe-now.png';
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class);
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function registerMediaCollections(): void
    {

        $this->addMediaCollection('english_subtitle');
        $this->addMediaCollection('hindi_subtitle');

        $this
            ->addMediaCollection('video')
            ->singleFile();

        $this
            ->addMediaCollection('thumbnail')
            ->useFallbackUrl(asset('assets/images/image-not-available.png'))
            ->singleFile();
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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->status = self::STATUS_DRAFT;

            if (!$model->min_age) $model->min_age = 1;

            if ($model->series_id && $model->language_id) {
                $previousEpisodeNumber = Video::query()
                    ->where('series_id', $model->series_id)
                    ->where('language_id', $model->language_id)
                    ->pluck('episode_number')
                    ->max();
                $model->episode_number = $previousEpisodeNumber + 1;
            }
        });

        static::created(function ($model) {
            if ($model->uploaded_video_path) {
                StartAWSTranscodingVideoJob::dispatch($model);
            }
        });

        static::updating(function ($model) {
            if (!$model->min_age) $model->min_age = 1;
        });

        static::updated(function ($model) {
            if ($model->isDirty('uploaded_video_path')) {
                StartAWSTranscodingVideoJob::dispatch($model);
            }
        });

        static::deleted(function ($model) {
            $model->deleted_by = auth()->id();
            $model->save();
        });
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeStreamable($query)
    {
        return $query->whereNotNull('streamable_video_path');
    }


    public function getSubtitles(): array
    {
        $subtitles = [];
        $languages = ['hindi', 'english'];
        foreach($languages as $language)
        {
            if($this->getFirstMediaUrl($language. '_subtitle'))
            {
                $subtitles[$language] = $this->getFirstMediaUrl($language. '_subtitle');
            }
        }
        return $subtitles;
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



