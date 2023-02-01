<?php

namespace App\Models;

use App\Traits\HasLast;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\User
 *
 * @property int $pin
 * @property int $active_profile_id
 * @property string $name
 */

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use AuthenticationLoggable;
    use HasUUID;
    use HasLast;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'user_type',
        'active_profile_id',
        'password',
        'otp',
        'uuid',
        'pin',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $guard_name = 'web';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static $recordEvents = ['updated'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'password'])
            ->setDescriptionForEvent(function ($eventName) {
                if ($eventName == 'updated') {
                    return 'his account details';
                }
            })
            ->logOnlyDirty();
    }

    public function isSuperAdmin(): bool
    {
        return true;
    }

    public function getActiveProfile(): ?Profile
    {
        if ($this->active_profile_id) {
            return Profile::find($this->active_profile_id);
        }

        return null;
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    // TODO - Subscriptions related methods must be updated to include profile.
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)
            ->where('status', Subscription::STATUS_ACTIVE);
    }

    public function isActiveSubscriber(): bool
    {
        if ($this->getFirstActiveSubscription()) {
            return true;
        }

        return false;
    }

    public function getFirstActiveSubscription(): ?Subscription
    {
        return $this
            ->activeSubscriptions
            ->first();
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function searches(): HasMany
    {
        return $this->hasMany(Search::class);
    }

    protected static function booted()
    {
        static::created(function ($model) {
            if ($model->user_type == 'user') {
                $profile = $model->profiles()->create([
                    'name' => 'Default Profile',
                    'dob' => '2012-02-25',
                ]);

                $model->active_profile_id = $profile->id;
                $model->save();
            }
            PendingUser::whereEmail($model->email)
                ->first()?->delete();
        });
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

}
