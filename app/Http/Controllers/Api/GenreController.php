<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $genres = $limit == -1
            ? Genre::where('status', Genre::STATUS_PUBLISHED)->get()
            : Genre::query()
                ->with(['streamableSeries', 'streamableShorts'])
                ->whereHas('streamableSeries')
                ->orWhereHas('streamableShorts')
                ->get();

        return GenreResource::collection($genres);
    }

    public function show(Genre $genre)
    {
        activity()
            ->performedOn($genre)
            ->event('visited')
            ->log('the genre screen');

        return GenreResource::make($genre);
    }
}
