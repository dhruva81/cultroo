<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Note these are plans from my razorpay test account

//        $plans = [
//            [
//                'pg_name' => 'Yearly Plan',
//                'pg_plan_id' => 'plan_Jxubor3lOBGiE7',
//                'is_active' => true,
//                'pg_billing_amount' =>  999,
//                'pg_billing_interval' => 1,
//                'pg_billing_period' => Plan::BILLING_PERIOD_MONTHLY,
//                'payment_gateway' => Plan::PAYMENT_GATEWAY_RAZORPAY,
//                'meta' => [
//                    'name' => 'Standard',
//
//                ]
//            ],
//            [
//                'pg_name' => 'Test Weekly 2 plan',
//                'pg_plan_id' => 'plan_JxswdMmaSi70WX',
//                'is_active' => true,
//                'pg_billing_amount' =>  70,
//                'pg_billing_interval' => 1,
//                'pg_billing_period' => Plan::BILLING_PERIOD_YEARLY,
//                'payment_gateway' => Plan::PAYMENT_GATEWAY_RAZORPAY,
//                'meta' => [
//                    'name' => 'Standard'
//                ]
//            ],
//
//        ];
//
//        foreach($plans as $plan)
//        {
//            Plan::firstOrCreate(
//                [
//                    'pg_plan_id' => $plan['pg_plan_id']
//                ],
//                $plan
//            );
//        }

        $response = Plan::getAllRazorpayPlans();

        $planIds = Plan::pluck('pg_plan_id')->toArray();

        foreach ($response['items'] as $plan) {
            if (! in_array($plan['id'], $planIds)) {
                Plan::create([
                    'pg_name' => $plan['item']['name'],
                    'pg_description' => $plan['item']['description'],
                    'pg_billing_amount' => $plan['item']['amount']/100,
                    'pg_billing_period' => $plan['period'],
                    'pg_plan_id' => $plan['id'],
                    'payment_gateway' => Plan::PAYMENT_GATEWAY_RAZORPAY,
                    'meta' => [
                        'name' => $plan['item']['name'],
                        'description' => $plan['item']['description'],
                    ],
                ]);
            }
        }
    }
}
