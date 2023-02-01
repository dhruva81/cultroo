<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user' => UserResource::make(auth()->user()),
            'profiles' => ProfileResource::collection(auth()->user()->profiles),
            'subscription' => SubscriptionResource::make(auth()->user()->getFirstActiveSubscription()),
        ];
    }
}
