<?php

namespace App\Livewire\Series;

use App\Models\Series;
use Livewire\Component;

class SeriesSlideoverDrawer extends Component
{
    public $showSeriesModal = false;

    public ?Series $series = null;

    public array $items = [];

    protected $listeners = [
        'showSeriesSlideOver' => 'showSeriesSlideOver',
    ];

    public function showSeriesSlideOver($seriesId = null)
    {
        $this->showSeriesModal = true;
        $this->series = Series::find($seriesId);
        $this->items = [
            [
                'label' => 'Total Episodes',
                'value' => $this->series->videos->count(),
                'visible' => true,
            ],
            [
                'label' => 'Episodes on App',
                'value' => $this->series->publishedAndStreamableVideos->count(),
                'visible' => $this->series->badge_status === 'published',
            ],
            [
                'label' => 'Total Seasons',
                'value' => $this->series->getSeasons()->count(),
                'visible' => true,
            ],
            [
                'label' => 'Seasons on App',
                'value' => $this->series->getPublishedSeasons()->count(),
                'visible' => true,
            ],
        ];
    }

    public function render()
    {
        return view('app.series.series-slideover-drawer');
    }
}
