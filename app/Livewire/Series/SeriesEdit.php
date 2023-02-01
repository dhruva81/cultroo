<?php

namespace App\Livewire\Series;

use App\Models\Series;
use Filament\Forms;
use Livewire\Component;

class SeriesEdit extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Series $series = null;

    public ?string $title = null;

    public ?string $short_description = null;

    public ?string $description = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->series ?
                [
                    'title' => $this->series->title,
                    'short_description' => $this->series->short_description,
                    'description' => $this->series->description,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('Series Title')
                ->required(),
            Forms\Components\Textarea::make('short_description'),
            Forms\Components\Textarea::make('description'),
        ];
    }

    public function submit()
    {
        if ($this->series) {
            $this->series->update($this->form->getState());
            session()->flash('success', 'Series updated successfully!');

            return redirect()->route('series.show', $this->series);
        }

        $batch = Series::create($this->form->getState());
        session()->flash('success', 'Series created successfully!');

        return redirect()->route('series.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div>

                @include('partials.headers.series_header')

                <div class="max-w-3xl my-8">

                    <x-header-simple title="Edit"  />

                    <form wire:submit.prevent="submit">

                            {{ $this->form }}

                            <x-button-loader
                                  class="my-1 w-full mt-8"
                                  color="indigo"
                                  size="xl"
                                  type="submit"
                                  wire:target="submit"
                                  >
                                 Submit
                            </x-button-loader>
                      </form>
                  </div>

            </div>
        blade;
    }
}
