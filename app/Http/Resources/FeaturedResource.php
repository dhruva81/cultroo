<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class FeaturedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(get_class($this->resource) === 'App\Models\Series')
        {
            $type = 'series';
        }
        else
        {
            $type = 'shorts';
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'badge' => Arr::random(['new', 'popular', 'trending']),
            'color' => $this->color,
            'type' => $type,
        ];
    }
}
