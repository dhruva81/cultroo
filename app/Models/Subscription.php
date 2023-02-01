<?php

namespace App\Models;

use App\Traits\HasLast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Razorpay\Api\Api as RazorpayApi;

class Subscription extends Model
{
    use HasFactory;
    use HasLast;

    protected $fillable = [
        'payment_gateway',
        'pg_subscription_id',
        'pg_plan_id',
        'profile_id',
        'user_id',
        'plan_id',
        'status',
        'ends_at',
    ];

    const STATUS_ACTIVE = 1;

    const STATUS_PENDING = 2;

    const STATUS_EXPIRED = 3;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public static function getAllRazorpaySubscriptions()
    {
        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));

        return $api->subscription->all()->toArray();
    }

    public static function getRazorpaySubscriptionById($id)
    {
        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
        try {
            return $api->subscription->fetch($id)->toArray();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

//    This method needs some updation
    public static function createRazorpaySubscription($data)
    {
        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
        try {
            $response = $api->subscription->create([
                'plan_id' => 'plan_JxaoDMHWOXiWqC',
                'total_count' => 12,
                'quantity' => 1,
                'start_at' => now(),
                'expire_by' => null,
                'customer_notify' => 1,
                'addons' => [
                    [
                        'item' => [
                            'name' => 'Delivery charges',
                            'amount' => 30000,
                            'currency' => 'INR',
                        ],
                    ],
                ],
                'offer_id' => null,
                'notes' => [
                    'notes_key_1' => 'Tea, Earl Grey, Hot',
                    'notes_key_2' => 'Tea, Earl Grey… decaf.',
                ],
                'notify_info' => [
                    'notify_phone' => '9993171325',
                    'notify_email' => 'amitjainmu@gmail.com',
                ],
            ]);

            Subscription::create([
                'subscription_id' => $response['id'],
            ]);

            return true;
        } catch (\Exception $e) {
            ray($e->getMessage());

            return false;
        }
    }

    public static function cancelRazorpaySubscription($subscriptionId)
    {
        try {
            $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
            $response = $api->subscription->fetch($subscriptionId)->cancel();

            return $response->toArray();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function updateRazorpaySubscription($subscriptionId, $options = null)
    {
        try {
            $options = [
                'status' => 'active',
            ];
            $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
            $response = $api->subscription->fetch($subscriptionId)->update($options);

            return $response->toArray();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getAllInvoicesForRazorpaySubscription($subscriptionId)
    {
        try {
            $options = [
                'status' => 'active',
            ];
            $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
            $response = $api->subscription->fetch($subscriptionId)->update($options);

            return $response->toArray();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSubscriptionDates(): array
    {

    }
}
