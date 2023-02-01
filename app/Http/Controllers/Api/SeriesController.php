<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use App\Models\Bookmark;
use App\Models\Series;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $series = Series::query()
            ->with('videos')
            ->withCount('publishedAndStreamableVideos')
            ->published()
            ->inRandomOrder()
            ->has('publishedAndStreamableVideos', '>', 0)
            ->paginate(100);

        return SeriesResource::collection($series);
    }

    public function show(Request $request, Series $series)
    {
        activity()
            ->performedOn($series)
            ->event('visited')
            ->log('the series screen');

        return SeriesResource::make($series, $request);
    }

    public function toggleBookmark(Series $series, $type = null)
    {
        $series->toggleBookmark($type);

        return response()->json([
            'message' => 'success',
        ], 201);
    }

}
