<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'status' => 'active',
            'plan_name' => $this->plan?->meta['name'],
            'plan_type' => $this->plan?->pg_billing_period,
            'plan_id' => $this->plan?->pg_plan_id,
            'next_billing_date' => null,
            'expires_at' => null,
        ];
    }
}
