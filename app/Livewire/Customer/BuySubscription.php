<?php

namespace App\Livewire\Customer;

use App\Models\Plan;
use App\Models\Subscription;
use Livewire\Component;
use Razorpay\Api\Api as RazorpayApi;

class BuySubscription extends Component
{
    public $plans = null;

    public $planId = null;

    public $subscription_id = null;

    public $amount = null;

    public $response = null;

    protected $listeners = ['capturePayment'];

    public function mount()
    {
        $this->plans = Plan::where('is_active', true)->get();
    }

    public function submit()
    {
        ray($this->planId);
        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));
        $response = null;
        try {
            $response = $api->subscription->create([
                'plan_id' => $this->planId,
                'total_count' => 12,
                'quantity' => 1,
                'start_at' => now(),
                'expire_by' => null,
                'customer_notify' => 1,
            ])->toArray();
        } catch (\Exception $e) {
            ray($e->getMessage());

            return false;
        }

        ray($response);
        if ($response) {
            $this->subscription_id = $response['id'];
            Subscription::create([
                'pg_subscription_id' => $response['id'],
                'pg_plan_id' => $this->planId,
                'user_id' => auth()->user()->id,
                'payment_gateway' => 1,
            ]);
        }

        $this->dispatchBrowserEvent('capturePayment');
    }

    public function capturePayment()
    {
//        $payment = \App\Models\Payment::create([
//            'user_id'   => auth()->user()->id,
//            'payment_gateway_provider' => 'razorpay',
//            'pg_payment_id' => $this->response["razorpay_payment_id"],
//            'pg_subscription_id' => $this->response["razorpay_subscription_id"],
//            'pg_signature' => $this->response["razorpay_signature"],
//        ]);
    }

    public function render()
    {
        return view('app.customer.buy-subscription')
                ->layout('layouts.customer');
    }
}
