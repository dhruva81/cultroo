<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use App\Models\Video;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 6;

        $languages = $limit == -1
            ? Language::active()->get()
            : Video::query()
                ->with('language')
                ->whereNotNull('streamable_video_path')
                ->whereStatus(1)
                ->get()
                ->pluck('language')
                ->unique('id');

        return LanguageResource::collection($languages);
    }

    public function show(Language $language)
    {
        activity()
            ->performedOn($language)
            ->event('visited')
            ->log('the language screen');

        return LanguageResource::make($language);
    }
}
