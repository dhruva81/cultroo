<?php

namespace App\Livewire\Videos;

use App\Models\Video;
use Livewire\Component;

class VideoShow extends Component
{
    public Video $video;

    public ?string $tab = null;

    public function mount()
    {
        $this->tab = request()->has('tab') && in_array(request('tab'), ['edit'])
            ? request()->get('tab')
            : 'show';
    }

    public function render()
    {
        return <<<'blade'
            <div>

                @include('app.videos.video-header')

                @if($tab === 'edit')
                     <livewire:videos.video-create-or-update :video="$video" />
                @else
                   @include('app.videos.video-show')
                @endif

            </div>
        blade;
    }
}
