<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->resource instanceof \App\Models\Series) {
            $type = 'series';
            $color = $this->color;
        }
        if ($this->resource instanceof \App\Models\Video) {
            $type = 'video';
        }

        return [
            'id' => $this?->id,
            'title' => $this?->title,
            'thumbnail' => $this?->getFirstMediaUrl('thumbnail'),
            'type' => $type ?? null,
            'color' => $color ?? null,
        ];
    }
}
