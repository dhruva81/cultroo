<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KollectionResource extends JsonResource
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
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->getFirstMediaUrl('icon'),
            'cover_image' => $this->getFirstMediaUrl('cover_image'),
            $this->mergeWhen($request->routeIs('search'), [
                'shorts_count' => $this->shorts_count,
                'series_count' => $this->series_count,
            ]),
            $this->mergeWhen($request->routeIs('collections.show'), [
                'description' => $this->description,
                'series' => SeasonResource::collection($this->getStreamableSeries()),
                'shorts' => VideoResource::collection($this->getStreamableShorts()),
            ]),
        ];
    }
}
