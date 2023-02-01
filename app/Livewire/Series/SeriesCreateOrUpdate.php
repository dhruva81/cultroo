<?php

namespace App\Livewire\Series;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Language;
use App\Models\Series;
use App\Models\Tag;
use App\Rules\OTPValidationRule;
use App\Rules\TagValidationRule;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component;

class SeriesCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Series $series = null;

    public ?string $title = null;

    public ?string $synopsis = null;

    public ?string $description = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->series ?
                [
                    'title' => $this->series->title,
                    'synopsis' => $this->series->synopsis,
                    'description' => $this->series->description,
                    'min_age' => $this->series->min_age,
                    'max_age' => $this->series->max_age,
                    'color' => $this->series->color,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make([
                'default' => 1,
                'sm' => 3,
            ])
                ->schema([
                    Forms\Components\Group::make()
                        ->columnSpan(2)
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->helperText('Word count: maximum 65 characters')
                                        ->label('Series Title')
                                        ->maxLength(65)
                                        ->unique('series', 'title', ignorable: $this->series)
                                        ->required(),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Textarea::make('description')
                                        ->minLength(60)
                                        ->maxLength(308)
                                        ->helperText('Word count: 60-308 characters'),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Textarea::make('synopsis')
                                        ->minLength(80)
                                        ->maxLength(408)
                                        ->helperText('Word count: 80-408 characters')
                                ]),
                        ]),
                    Forms\Components\Group::make()
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                                        ->image()
                                        ->visibility('public')
                                        ->collection('thumbnail')
                                        ->preserveFilenames()
                                        ->disk('s3'),

                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\ColorPicker::make('color')
                                        ->label('Thumbnail Background Color'),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('genres')
                                        ->multiple()
                                        ->label('Category')
                                        ->relationship('genres', 'name')
                                        ->placeholder('Select Category')
                                        ->options(Genre::pluck('name', 'id')),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('tags')
                                        ->multiple()
                                        ->relationship('tags', 'name')
                                        ->options(Tag::pluck('name', 'id'))
                                        ->placeholder('Select a tag')
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->disableLabel()
                                                ->disableAutocomplete()
                                                ->placeholder('Enter a tag')
                                                ->required()
                                                ->rules([fn ($state) => new TagValidationRule($state)])
                                                ->dehydrateStateUsing(fn ($state) => str_replace('#', '', strtolower($state)))
                                        ])
                                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                            return $action
                                                ->modalHeading('Create tag')
                                                ->modalButton('Create')
                                                ->modalWidth('lg');
                                        })
                                        ->searchable(),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('collections')
                                        ->relationship('collections', 'name')
                                        ->multiple()
                                        ->placeholder('Select collections')
                                        ->options(Kollection::pluck('name', 'id')),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('min_age')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(1),
                                        Forms\Components\TextInput::make('max_age')
                                            ->numeric()
                                            ->gte('min_age'),
                                    ])
                                ]),
                        ]),


                ]),
        ];
    }

    protected function getFormModel(): Series|string
    {
        return $this->series ? $this->series : Series::class;
    }

    public function submit()
    {
        if ($this->series) {
            $this->series->update($this->form->getState());
            $this->form->model($this->series)->saveRelationships();
            session()->flash('success', 'Series updated successfully!');

            return redirect()->route('series.show', ['series' => $this->series, 'tab' => 'meta']);
        }

        $series = Series::create($this->form->getState() + ['status' => 1]);
        $this->form->model($series)->saveRelationships();
        session()->flash('success', 'Series created successfully!');

        return redirect()->route('series.show', ['series' => $series, 'tab' => 'videos']);
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-7xl mx-auto my-8">

                  <x-header-simple title="{{ $this->series ? 'Edit Series' : 'Create Series' }}" />

                  <form wire:submit.prevent="submit">

                        {{ $this->form }}

                        <x-button-loader
                              class="my-1 mt-8"
                              color="indigo"
                              size="xl"
                              type="submit"
                              wire:target="submit"
                              >
                             Submit
                        </x-button-loader>
                  </form>

                  {{ $this->modal }}

            </div>
        blade;
    }
}
