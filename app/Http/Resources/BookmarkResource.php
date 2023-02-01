<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->bookmarkable_type === 'series')
        {
            return new SeriesResource($this->bookmarkable);
        }

        if($this->bookmarkable_type === 'video')
        {
            return new VideoResource($this->bookmarkable);
        }

    }
}
