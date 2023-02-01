<?php

namespace App\Livewire\Collections;

use App\Models\Kollection;
use Filament\Forms;
use Livewire\Component;

class CollectionCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Kollection $collection = null;

    public ?string $name = null;

    public ?string $status = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->collection ?
                [
                    'name' => $this->collection->name,
                    'color' => $this->collection->color,
                    'status' => $this->collection->status,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Collection Name')
                ->unique('collections', 'name', ignorable: $this->collection)
                ->required(),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Select::make('status')
                        ->required()
                        ->placeholder('Select Status')
                        ->label('Status')
                        ->default(Kollection::STATUS_DRAFT)
                        ->options([
                            Kollection::STATUS_DRAFT => 'Draft',
                            Kollection::STATUS_REVIEW => 'Review',
                            Kollection::STATUS_PUBLISHED => 'Published',
                        ]),
                    Forms\Components\ColorPicker::make('color')
                        ->required(),
                ]),
            Forms\Components\SpatieMediaLibraryFileUpload::make('icon')
                ->label('Icon')
                ->image()
                ->visibility('public')
                ->collection('icon')
                ->preserveFilenames()
                ->disk('s3'),
            Forms\Components\SpatieMediaLibraryFileUpload::make('cover_image')
                ->label('Thumbnail')
                ->image()
                ->visibility('public')
                ->collection('thumbnail')
                ->preserveFilenames()
                ->disk('s3'),
        ];
    }

    protected function getFormModel(): Kollection|string
    {
        return $this->collection ? $this->collection : Kollection::class;
    }

    public function submit()
    {
        if ($this->collection) {
            $this->collection->update($this->form->getState());
            $this->form->model($this->collection)->saveRelationships();

            session()->flash('success', 'Collection updated successfully!');

            return redirect()->route('collections.datatable');
        }

        $collection = Kollection::create($this->form->getState() + ['status' => 'draft']);
        $this->form->model($collection)->saveRelationships();

        session()->flash('success', 'Collection created successfully!');

        return redirect()->route('collections.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="{{ $this->collection ? 'Edit Collection' : 'Create Collection' }}">
                        <x-href href="{{route('collections.datatable')  }}">
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
