<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'dob' => $this->dob?->format('d-m-Y'),
            'age' => $this->dob ? Carbon::parse($this->dob)->age : null,
            'avatar' => $this->getAvatar(),
            'pin' => (bool) $this->pin,
            $this->mergeWhen($request->routeIs('account', 'profiles.show', 'profiles.active'), [
                'tracking_watch_history' => $this->tracking_watch_history,
                'tracking_search_history' => $this->tracking_search_history,
            ]),
            'active_profile' => $this->user->active_profile_id === $this->id,
            'languages' => LanguageResource::collection($this->languages),
            'genres' => GenreResource::collection($this->genres),
        ];
    }
}
