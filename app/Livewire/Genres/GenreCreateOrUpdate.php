<?php

namespace App\Livewire\Genres;

use App\Models\Genre;
use Filament\Forms;
use Livewire\Component;

class GenreCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Genre $genre = null;

    public ?string $name = null;

    public ?string $status = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->genre ?
                [
                    'name' => $this->genre->name,
                    'color' => $this->genre->color,
                    'slug' => $this->genre->slug,
                    'status' => $this->genre->status,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Category Name')
                ->unique('genres', 'name', ignorable: $this->genre)
                ->dehydrateStateUsing(fn ($state) => ucwords($state))
                ->required(),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Select::make('status')
                        ->required()
                        ->placeholder('Select Status')
                        ->label('Status')
                        ->default(Genre::STATUS_DRAFT)
                        ->options([
                            Genre::STATUS_DRAFT => 'Draft',
                            Genre::STATUS_REVIEW => 'Review',
                            Genre::STATUS_PUBLISHED => 'Published',
                        ]),
                    Forms\Components\ColorPicker::make('color')
                        ->required(),
                ]),
            Forms\Components\SpatieMediaLibraryFileUpload::make('icon')
                ->image()
                ->label('Icon')
                ->visibility('public')
                ->collection('icon')
                ->preserveFilenames()
                ->disk('s3'),
            Forms\Components\SpatieMediaLibraryFileUpload::make('cover_image')
                ->image()
                ->label('Cover Image')
                ->visibility('public')
                ->collection('cover_image')
                ->preserveFilenames()
                ->disk('s3'),
        ];
    }

    protected function getFormModel(): Genre|string
    {
        return $this->genre ? $this->genre : Genre::class;
    }

    public function submit()
    {
        if ($this->genre) {
            $this->genre->update($this->form->getState());
            $this->form->model($this->genre)->saveRelationships();

            session()->flash('success', 'Genre updated successfully!');

            return redirect()->route('genres.datatable');
        }

        $genre = Genre::create($this->form->getState() + ['status' => 'draft']);
        $this->form->model($genre)->saveRelationships();

        session()->flash('success', 'Genre created successfully!');

        return redirect()->route('genres.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="{{ $this->genre ? 'Edit Genre' : 'Create Genre' }}">
                        <x-href href="{{route('genres.datatable')  }}">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go Back
                        </x-href>
                  </x-header-simple>

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
            </div>
        blade;
    }
}
