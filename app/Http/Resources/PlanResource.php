<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->meta['name'],
            'plan_id' => $this->pg_plan_id,
            'plan_type' => $this->pg_billing_period,
            'hightlight_title' => isset($this->meta['hightlight_title']) ? $this->meta['hightlight_title'] : null,
            'hightlight_subtitle' => isset($this->meta['hightlight_subtitle']) ? $this->meta['hightlight_subtitle'] : null,
            'description' => isset($this->meta['description']) ? $this->meta['description'] : null,
            'billing_amount' => $this->pg_billing_amount,
            'billing_interval' => $this->pg_billing_interval,
            'billing_period' => $this->pg_billing_period,
            'icon' => $this->getFirstMediaUrl('icon'),
            'featured' => $this->is_featured,
            'features' => $this->getPlanFeatures(),
        ];
    }
}
