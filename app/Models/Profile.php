<?php

namespace App\Models;

use App\Traits\HasLast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasLast;
    use LogsActivity;

    protected $fillable = [
        'name',
        'dob',
        'is_active_profile',
        'pin',
        'avatar_id',
        'tracking_search_history',
        'tracking_watch_history',
    ];

    protected $dates = ['dob'];

    protected $casts = [
        'dob' => 'date',
        'pin' => 'integer',
        'is_active_profile' => 'bool',
        'avatar_id' => 'integer',
        'tracking_search_history' => 'bool',
        'tracking_watch_history' => 'bool',
    ];

    protected static $recordEvents = ['updated'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()

            ->logOnly(['name', 'dob', 'deleted_at'])
            ->setDescriptionForEvent(function ($eventName) {
                if ($eventName == 'updated') {
                    return 'his profile details';
                }
            })
            ->logOnlyDirty();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }

    public function genres(): MorphToMany
    {
        return $this->morphToMany( Genre::class, 'genreable');
    }

    public function searches(): HasMany
    {
        return $this->hasMany(Search::class);
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(Avatar::class);
    }

    public function getAvatar(): ?string
    {
        if($this->avatar?->avatar_path) {
            return Storage::disk('s3')->url($this->avatar?->avatar_path);
        }

        return asset('assets/images/avatar.png');

    }

}
