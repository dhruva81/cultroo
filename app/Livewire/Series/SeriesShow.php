<?php

namespace App\Livewire\Series;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Series;
use App\Models\Video;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class SeriesShow extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public Series $series;

    public ?string $tab = null;

    public ?int $languageId = null;

    public bool $showVideosWithoutLanguage = false;

    public function mount()
    {
        $this->tab = request()->has('tab') && in_array(request('tab'), ['meta', 'edit'])
            ? request()->get('tab')
            : 'show';

        if(request()->has('language'))
        {
            $this->languageId = request()->get('language');
        }
    }

    public function render()
    {
        return <<<'blade'
            <div>

                @include('app.series.series-header')

                @if($tab === 'meta')
                    @includeIf('app.series.series-show')
                @elseif($tab === 'edit')
                     <livewire:series.series-create-or-update
                        :series="$series"
                        />
                @else
                    <livewire:videos.videos-datatable
                        :series="$series"
                        :languageId="$languageId"
                        :showVideosWithoutLanguage="$showVideosWithoutLanguage"
                        />
                @endif

            </div>
        blade;
    }

}
