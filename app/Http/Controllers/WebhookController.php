<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateVideoTranscodingStatus;
use App\Models\AWSMediaConvertStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Aws\Sns\Exception\InvalidSnsMessageException;

class WebhookController extends Controller
{
    public function subscribe(Request $request)
    {
        if (config('app.webhook_client_secret') !== $request->header('signature')) {
            return response()->json([
                'message' => 'Signature invalid.',
            ], 403);
        }

        $plan_id = Plan::where('pg_plan_id', $request->plan_id)->first()?->id;

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'profile_id' => ['required', 'exists:profiles,id'],
            'pg_subscription_id' => ['required', 'unique:subscriptions,pg_subscription_id'],
            'pg_plan_id' => ['required'],
        ]);

        Subscription::create([
            'user_id' => $request->user_id,
            'profile_id' => $request->profile_id,
            'pg_subscription_id' => $request->pg_subscription_id,
            'pg_plan_id' => $request->pg_plan_id,
            'plan_id' => $plan_id,
            'payment_gateway' => 1,
        ]);

        return response()->json([
            'message' => 'Success',
        ], 201);
    }

    /**
     *
     * @hidden
     */
    public function createAwsMediaConvertStatus(Request $request)
    {
//        $body = json_decode($request->getContent(),true);

        // Instantiate the Message and Validator
        $message = Message::fromRawPostData();
        $validator = new MessageValidator();

        // Validate the message and log errors if invalid.
        try {
            $validator->validate($message);
        } catch (InvalidSnsMessageException $e) {
            // Pretend we're not here if the message is invalid.
            http_response_code(404);
            Log::error('SNS Message Validation Error: ' . $e->getMessage());
            die();
        }

        // Check the type of the message and handle the subscription.
        if ($message['Type'] === 'SubscriptionConfirmation') {
            // Confirm the subscription by sending a GET request to the SubscribeURL
            Log::info('SNS Subscription Confirmation: ' . $message['SubscribeURL']);
        }

        if ($message['Type'] === 'Notification') {
            // Do whatever you want with the message body and data.
            $message = json_decode($message['Message'],true);
            UpdateVideoTranscodingStatus::dispatch($message);
        }

    }
}
