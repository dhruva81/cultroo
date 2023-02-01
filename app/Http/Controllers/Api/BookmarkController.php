<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function favourites()
    {
        $bookmarks = auth()->user()
            ->bookmarks()
            ->where('type', Bookmark::TYPE_FAVOURITE)
            ->where('profile_id', auth()->user()->getActiveProfile()?->id)
            ->paginate(10);

        return BookmarkResource::collection($bookmarks);
    }

    public function watchlist()
    {
        $bookmarks = auth()->user()
            ->bookmarks()
            ->where('type', Bookmark::TYPE_WATCHLIST)
            ->where('profile_id', auth()->user()->getActiveProfile()?->id)
            ->paginate(10);

        return BookmarkResource::collection($bookmarks);
    }
}
