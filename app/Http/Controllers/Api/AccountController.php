<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use http\Env\Request;

class AccountController extends Controller
{
    // TODO - This endpoint response is not good.

    /**
     * Is Active Subscriber?
     */
    public function isActiveSubscriber()
    {
        return auth()->user()->isActiveSubscriber();
    }

    /**
     * My Account
     */
    public function account()
    {
        // This endpoint is in very early stage.
        // It will be updated soon.

        activity()
            ->event('visited')
            ->log('his account page.');

        return AccountResource::make(auth()->user());
    }

    /**
     * Update Account
     */
    public function update(\Illuminate\Http\Request $request)
    {
        if($request->has('name') && $request->name !== null) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);

            auth()->user()->name = $request->name;
        }

        if($request->has('display_name') && $request->display_name !== null) {
            $request->validate([
                'display_name' => ['required', 'string', 'max:255'],
            ]);

            auth()->user()->display_name = $request->display_name;
        }

        auth()->user()->save();

        return response()->json([
            'message' => 'Account updated successfully!',
        ], 201);
    }
}
