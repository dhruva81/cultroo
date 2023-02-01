<?php

namespace App\Livewire\Sections;

use App\Models\Section;
use Livewire\Component;

class SectionShow extends Component
{
    public Section $section;

    public ?string $tab = null;

    public function render()
    {
        return <<<'blade'
            <div>

                 @include('app.sections.section-header')

                 @if($this->section->model === 'series') <livewire:series.series-datatable :section="$section" />
                 @elseif($this->section->model === 'video') <livewire:videos.videos-datatable :section="$section" />
                 @elseif($this->section->model === 'character') <livewire:characters.characters-datatable :section="$section" />
                 @elseif($this->section->model === 'language') <livewire:languages.languages-datatable :section="$section" />
                 @endif

            </div>
        blade;
    }
}
