<?php

namespace App\Livewire\Characters;

use App\Models\Character;
use Filament\Forms;
use Livewire\Component;

class CharacterCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Character $character = null;

    public ?string $name = null;

    public ?string $short_description = null;

    public ?string $description = null;

    public string $status = 'draft';

//    public array $categories = [];

    public function mount(): void
    {
        $this->form->fill(
            $this->character ?
                [
                    'name' => $this->character->name,
                    'short_description' => $this->character->short_description,
                    'description' => $this->character->description,
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
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Character Name')
                                        ->unique('characters', 'name', ignorable: $this->character)
                                        ->dehydrateStateUsing(fn ($state) => ucwords($state))
                                        ->required(),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Textarea::make('short_description'),
                                    Forms\Components\Textarea::make('description'),
                                ]),
                        ])
                        ->columnSpan(2),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                                        ->visibility('public')
                                        ->collection('photo')
                                        ->preserveFilenames()
                                        ->disk('s3'),
                                ]),
                        ])
                        ->columnSpan(1),
                ]),
        ];
    }

    protected function getFormModel(): Character|string
    {
        return $this->character ? $this->character : Character::class;
    }

    public function submit()
    {
        if ($this->character) {
            $this->character->update($this->form->getState());
            session()->flash('success', 'Chapter updated successfully!');

            return redirect()->route('characters.datatable');
        }

        $character = Character::create($this->form->getState() + ['status' => Character::STATUS_DRAFT]);
        $this->form->model($character)->saveRelationships();
        session()->flash('success', 'Character created successfully!');

        return redirect()->route('characters.datatable');
    }

    public function render()
    {
        return <<<'blade'
             <div class="max-w-7xl mx-auto my-8">

                  <x-header-simple title="{{ $this->character ? 'Edit Character' : 'Create Character' }}">
                        <x-href href="{{ route('characters.datatable')  }}">
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
