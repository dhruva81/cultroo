<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
use Livewire\Component;

class TagShow extends Component
{
    public Tag $tag;

    public ?string $tab = null;

    public function mount()
    {
        $this->tab = request()->has('tab') && in_array(request('tab'), ['videos', 'series'])
            ? request()->get('tab')
            : 'videos';
    }


    public function render()
    {
        return <<<'blade'
            <div>

                 @include('app.tags.tag-header')

                 @if($tab === 'series')
                    <livewire:series.series-datatable :tag="$tag" />
                 @else
                   <livewire:videos.videos-datatable :tag="$tag" />
                @endif

            </div>
        blade;
    }
}
