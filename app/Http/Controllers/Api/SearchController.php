<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KollectionResource;
use App\Http\Resources\SearchResource;
use App\Http\Resources\SeriesResource;
use App\Http\Resources\VideoResource;
use App\Models\Kollection;
use App\Models\Search;
use App\Models\Series;
use App\Models\Video;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $series = Series::query()
                    ->with(['videos', 'tags', 'genres'])
                    ->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('description', 'like', '%'.$request->q.'%')
                    ->orWhereRelation('tags', 'name', 'like', '%'.$request->q.'%')
                    ->orWhereRelation('genres', 'name', 'like', '%'.$request->q.'%')
                    ->orWhereRelation('videos', 'title', 'like', '%'.$request->q.'%')
                    ->inRandomOrder()
                    ->published()
                    ->has('publishedAndStreamableVideos', '>', 0)
                    ->get()
                    ->take(10);

        $shorts = Video::query()
                    ->whereNull('series_id')
                    ->where('title', 'like', '%'.$request->q.'%')
                    ->inRandomOrder()
                    ->published()
                    ->streamable()
                    ->get()
                    ->take(10);

        $kollections = Kollection::query()
                    ->withCount(['series', 'shorts'])
                    ->where('name', 'like', '%'.$request->q.'%')
                    ->inRandomOrder()
                    ->get()
                    ->take(10);

        $activeProfile = auth()->user()?->getActiveProfile();

        Search::firstOrCreate([
            'search_term' => $request->q,
            'user_id' => auth()->id(),
            'profile_id' => $activeProfile?->id,
            'is_visible' => $activeProfile?->tracking_search_history,
        ]);

        return [
            'series' => SeriesResource::collection($series),
            'shorts' => VideoResource::collection($shorts),
            'collections' => KollectionResource::collection($kollections),
        ];
    }

    public function history(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        if(auth()->user()->getActiveProfile())
        {
            $searches = auth()->user()
                    ->getActiveProfile()
                    ->searches()
                    ->visible()
                    ->latest()
                    ->paginate($limit);
        }
        else
        {
            $searches = auth()->user()
                    ->searches()
                    ->visible()
                    ->latest()
                    ->paginate($limit);
        }

        return SearchResource::collection($searches);
    }


    public function delete(Search $search): \Illuminate\Http\JsonResponse
    {
        $search->delete();

        return response()->json([
            'message' => 'Search term deleted successfully!',
        ], 200);
    }

}
