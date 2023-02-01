<?php

namespace App\Http\Resources;

use App\Enums\TranscodingStatus;
use App\Models\Bookmark;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // TODO - Update runtime to be in minutes and seconds
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'run_time' => '3:40',
            'free' => $this->is_free,
            'access' => $this->canWatchVideo(),
            'episode_number' => $this->when($this->series_id, $this->episode_number),
            'badge' => Arr::random(['new', 'popular', 'trending']),
            'type' => $this->series_id ? 'episode' : 'shorts',
            $this->mergeWhen($request->routeIs('videos.show'), [
                'favorite' => $this->isBookmarked(Bookmark::TYPE_FAVOURITE),
                'watchlist' => $this->isBookmarked(Bookmark::TYPE_WATCHLIST),
                'fallback_url' => $this->getFallBackUrl(),
                'video_url' => $this->getStreamableVideoUrl(),
            ]),
        ];
    }
}
