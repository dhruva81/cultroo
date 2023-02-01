<?php

namespace App\Livewire\Collections;

use App\Models\Kollection;
use Livewire\Component;

class CollectionShow extends Component
{
    public Kollection $collection;

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

                 @include('app.collections.collection-header')

                 @if($tab === 'series')
                    <livewire:series.series-datatable :collection="$collection" />
                 @else
                   <livewire:videos.videos-datatable :collection="$collection" />
                @endif

            </div>
        blade;
    }
}
