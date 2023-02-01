<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
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
            $this->mergeWhen($request->routeIs('genres.show'), [
                'series' => SeriesResource::collection($this->streamableSeries),
                'shorts' => VideoResource::collection($this->streamableShorts),
            ]),
        ];
    }
}
