<?php

namespace App\Http\Resources;

use App\Models\Bookmark;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class SeriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $languageId = $request->has('language') ? $request->get('language') : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'synopsis' => $this->synopsis,
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'episodes_count' => $this->publishedAndStreamableVideos->count(),
            'seasons_count' => $this->getPublishedSeasons()->count(),
            'season_number' => $this->season_number,
            'badge' => Arr::random(['new', 'popular', 'trending']),
            'type' => 'series',
            'color' => $this->color,
            $this->mergeWhen($request->routeIs('series.show'), [
                'favorite' => $this->isBookmarked(Bookmark::TYPE_FAVOURITE),
                'watchlist' => $this->isBookmarked(Bookmark::TYPE_WATCHLIST),
                'release_year' => 2022,
                'watch_time' => random_int(100, 1000),
                'languages' => LanguageResource::collection($this->getLanguagesOfPublishedAndStreamableVideos()),
                'characters' =>  CharacterResource::collection($this->characters),
                'genres' => GenreResource::collection($this->genres),
                'tags' => TagResource::collection($this->tags),
                'default_language' =>(int) $this->getDefaultLanguage()?->id,
                'episodes_language' => $languageId ? (int) $languageId : (int) $this->getDefaultLanguage()?->id,
                'episodes' => VideoResource::collection($this->getEpisodesByLanguage($languageId)),
                'seasons' => SeasonResource::collection($this->getPublishedSeasons()),
                'related' => [
                    'series' => RelatedResource::collection($this->getRelatedSeries()),
                    'shorts' => RelatedResource::collection($this->getRelatedShorts()),
                ],
            ]),
        ];
    }
}
