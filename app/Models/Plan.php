<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Traits\HasLast;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Razorpay\Api\Api as RazorpayApi;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Plan extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasUUID;
    use HasLast;

    protected $fillable = [
        'pg_name',
        'pg_description',
        'pg_plan_id',
        'pg_billing_amount',
        'pg_billing_period',
        'is_active',
        'is_free',
        'is_featured',
        'payment_gateway',
        'uuid',
        'meta',
    ];

    const BILLING_PERIOD_MONTHLY = 'monthly';

    const BILLING_PERIOD_YEARLY = 'yearly';

    const PAYMENT_GATEWAY_RAZORPAY = 1;

    protected $casts = [
        'meta' => 'json',
        'pg_billing_period' => 'string',
        'pg_billing_amount' => MoneyCast::class,
        'is_free' => 'bool',
        'is_featured' => 'bool',
        'is_active' => 'bool',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getPlanFeatures(): ?array
    {
        if (! isset($this->meta['features']) || ! is_array($this->meta['features']) || (isset($this->meta['features']) && count($this->meta['features']) === 0)) {
            return null;
        }

        return collect($this->meta['features'])->pluck('item')->toArray();
    }

    public static function getAllRazorpayPlans()
    {
        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));

        return $api->plan->all()->toArray();
    }

    public static function getRazorpayPlanById($id)
    {
        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
        try {
            return $api->plan->fetch($id)->toArray();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('icon')
            ->useFallbackUrl(asset('assets/images/image-not-available.png'))
            ->singleFile();

        $this
            ->addMediaCollection('thumbnail')
            ->useFallbackUrl(asset('assets/images/image-not-available.png'))
            ->singleFile();
    }
}
