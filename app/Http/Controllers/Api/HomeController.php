<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeaturedResource;
use App\Http\Resources\SeriesResource;
use App\Http\Resources\VideoResource;
use App\Models\Series;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function featured(Request $request)
    {
        $series = Series::query()
            ->with('videos')
            ->withCount('publishedAndStreamableVideos')
            ->has('publishedAndStreamableVideos', '>', 0)
            ->published()
            ->inRandomOrder()
            ->featured()
            ->take(10)
            ->get();

        $shorts = Video::query()
            ->published()
            ->streamable()
            ->inRandomOrder()
            ->featured()
            ->whereNull('series_id')
            ->take(10)
            ->get();

        $seriesResource = FeaturedResource::collection($series);
        $shortsResponse = FeaturedResource::collection($shorts);

        $mergedResponse = array_merge($seriesResource->toArray($request), $shortsResponse->toArray($request));

        return response()->json([
            'data' => $mergedResponse,
        ], 200);

    }

    public function latest(Request $request)
    {
        $series = Series::query()
            ->with('videos')
            ->withCount('publishedAndStreamableVideos')
            ->has('publishedAndStreamableVideos', '>', 0)
            ->published()
            ->inRandomOrder()
            ->take(10)
            ->get();

        return SeriesResource::collection($series);
    }

    public function comingSoon(Request $request)
    {
        $series = Series::query()
            ->with('videos')
            ->withCount('publishedAndStreamableVideos')
            ->has('publishedAndStreamableVideos', '>', 0)
            ->published()
            ->inRandomOrder()
            ->take(10)
            ->get();

        return SeriesResource::collection($series);
    }

    public function topPicksForYou(Request $request)
    {
        $series = Series::query()
            ->with('videos')
            ->withCount('publishedAndStreamableVideos')
            ->has('publishedAndStreamableVideos', '>', 0)
            ->published()
            ->inRandomOrder()
            ->take(10)
            ->get();

        return SeriesResource::collection($series);
    }

    public function continueWatching(Request $request)
    {
//        $series = Series::query()
//            ->with('videos')
//            ->withCount('publishedAndStreamableVideos')
//            ->has('publishedAndStreamableVideos', '>', 0)
//            ->published()
//            ->inRandomOrder()
//            ->take(10)
//            ->get();

        $series = collect();

        return SeriesResource::collection($series);
    }
}
