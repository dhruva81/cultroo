<?php

namespace App\Livewire\Dashboard;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Language;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use App\Models\WatchHistory;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public array $cards = [];

    public $mostWatchedVideos = null;

    public $mostActiveUsers = null;

    public function mount()
    {

//        $this->mostWatchedVideos = Video::query()
//            ->orderBy('watch_count', 'desc')
//            ->take(5)
//            ->get();
//
//        $this->mostActiveUsers = WatchHistory::with('user')
//            ->select('user_id')
//            ->groupBy('user_id')
//            ->orderByRaw('COUNT(*) DESC')
//            ->take(5)
//            ->get();

        $this->cards = [
            [
                'title' => 'Series',
                'count' => Series::count(),
                'sub_count' => Video::whereNotNull('series_id')->count() . ' Episodes',
                'icon' => 'heroicon-o-collection',
                'url' => route('series.datatable'),
            ],
            [
                'title' => 'Shorts',
                'count' => Video::whereNull('series_id')->count(),
                'icon' => 'heroicon-o-video-camera',
                'url' => route('shorts.datatable'),
            ],
            [
                'title' => 'Genres',
                'count' => Genre::count(),
                'icon' => 'heroicon-o-adjustments',
                'url' => route('genres.datatable'),
            ],
            [
                'title' => 'Characters',
                'count' => Character::count(),
                'icon' => 'heroicon-o-sparkles',
                'url' => route('characters.datatable'),
            ],
            [
                'title' => 'Tags',
                'count' => Tag::count(),
                'icon' => 'heroicon-o-tag',
                'url' => route('tags.datatable'),
            ],
            [
                'title' => 'Languages',
                'count' => Language::count(),
                'icon' => 'heroicon-o-translate',
                'url' => route('languages.datatable'),
            ],
            [
                'title' => 'Collections',
                'count' => Kollection::count(),
                'icon' => 'heroicon-o-color-swatch',
                'url' => route('collections.datatable'),
            ],

        ];
    }

    public function render()
    {
        return view('app.dashboard.index');
    }
}
