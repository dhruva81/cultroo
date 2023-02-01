<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Series;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show(Request $request, Video $video)
    {
        $video->increment('watch_count');

        $activeProfile = auth()->user()->getActiveProfile();

        auth()->user()->watchHistories()->create([
            'video_id' => $video->id,
            'profile_id' => $activeProfile?->id,
            'is_visible' => $activeProfile?->tracking_watch_history,
        ]);

        activity()
            ->performedOn($video)
            ->event('watched')
            ->log('the video');

        return VideoResource::make($video);
    }

    public function shorts(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 100;

        $videos = Video::query()
            ->published()
            ->streamable()
            ->inRandomOrder()
            ->whereNull('series_id')
            ->paginate($limit);

        return VideoResource::collection($videos);
    }

    public function toggleBookmark(Video $video, $type = null)
    {
        $video->toggleBookmark($type);

        return response()->json([
            'message' => 'success',
        ], 201);
    }
}
