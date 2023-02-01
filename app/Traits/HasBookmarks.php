<?php

namespace App\Traits;

use App\Models\Bookmark;
use Illuminate\Validation\ValidationException;

trait HasBookmarks
{
    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function isBookmarked(int $type = null): bool
    {
        if($type === null) return false;

        return $this->bookmarks()
            ->where('user_id', auth()->id())
            ->where('profile_id', auth()->user()->getActiveProfile()?->id)
            ->where('type', $type)
            ->exists();
    }

    // TODO - Fix this method
    public function toggleBookmark($type = null)
    {
        if(!$type) return null;

        if(!in_array($type, ['favourite', 'watchlist'])){
            throw ValidationException::withMessages([
                'type' => ['Type must be favourite or watchlist.'],
            ]);
        }

        if($type === 'favourite') {
            $bookmarkType = Bookmark::TYPE_FAVOURITE;
        } else {
            $bookmarkType = Bookmark::TYPE_WATCHLIST;
        }

        $bookmark = $this->bookmarks()
            ->where('user_id', auth()->id())
            ->where('profile_id', auth()->user()?->getActiveProfile()?->id)
            ->where('type', $bookmarkType)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
        } else {
            $this->bookmarks()->create([
                'user_id' => auth()->id(),
                'profile_id' => auth()->user()?->getActiveProfile()?->id,
                'type' => $bookmarkType,
            ]);
        }
    }
}
