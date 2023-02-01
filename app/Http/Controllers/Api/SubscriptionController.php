<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorpayApi;

class SubscriptionController extends Controller
{
    public function storeSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,pg_plan_id']
        ]);

        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));

        try {
            $response = $api->subscription->create([
                'plan_id' => $request->plan_id,
                'total_count' => 12,
                'quantity' => 1,
                'start_at' => now(),
                'expire_by' => null,
                'customer_notify' => 1,
            ])->toArray();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        $plan = Plan::where('pg_plan_id', $request->plan_id)->first();

        $subscription = Subscription::create([
            'user_id' => auth()->user()->id,
            'plan_id' => $plan?->id,
            'profile_id' => auth()->user()->getActiveProfile()?->id,
            'pg_subscription_id' => $response['id'],
            'pg_plan_id' => $request->plan_id,
            'payment_gateway' => 1,
            'status' => Subscription::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Success',
            'subscription_id' => $response['id'],
        ], 201);
    }

    public function confirmSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => ['required'],
        ]);

        $subscription = Subscription::where('pg_subscription_id', $request->subscription_id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => Subscription::STATUS_ACTIVE,
            ]);
        }

        return response()->json([
            'message' => 'Success',
        ], 201);
    }
}
