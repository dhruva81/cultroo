<?php

namespace App\Livewire\Genres;

use App\Models\Genre;
use Livewire\Component;

class GenreShow extends Component
{
    public Genre $genre;

    public ?string $tab = null;

    public function mount()
    {
        $this->tab = request()->has('tab') && in_array(request('tab'), ['shorts', 'series'])
            ? request()->get('tab')
            : 'series';
    }

    public function render()
    {
        return <<<'blade'
            <div>

                 @include('app.genres.genre-header')

                 @if($tab === 'series')
                    <livewire:series.series-datatable :genre="$genre" />
                 @else
                   <livewire:videos.videos-datatable :genre="$genre" />
                @endif

            </div>
        blade;
    }
}
