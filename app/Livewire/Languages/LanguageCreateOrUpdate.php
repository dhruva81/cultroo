<?php

namespace App\Livewire\Languages;

use App\Models\Language;
use Filament\Forms;
use Livewire\Component;

class LanguageCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Language $language = null;

    public ?string $name = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->language ?
                [
                    'name' => $this->language->name,
                    'is_active' => $this->language->is_active,
                    'status' => $this->language->status,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Language Name')
                ->unique('languages', 'name', ignorable: $this->language)
                ->dehydrateStateUsing(fn ($state) => ucwords($state))
                ->required(),
            Forms\Components\Select::make('status')
                ->required()
                ->placeholder('Select Status')
                ->label('Status')
                ->default(Language::STATUS_INACTIVE)
                ->options([
                    Language::STATUS_INACTIVE => 'Inactive',
                    Language::STATUS_ACTIVE => 'Active',
                ]),
        ];
    }

    public function submit()
    {
        if ($this->language) {
            $this->language->update($this->form->getState());
            session()->flash('success', 'Language updated successfully!');

            return redirect()->route('languages.datatable');
        }

        Language::create($this->form->getState() + ['status' => 'draft']);
        session()->flash('success', 'Language created successfully!');

        return redirect()->route('languages.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="{{ $this->language ? 'Edit Language' : 'Add Language' }}">
                        <x-href href="{{ route('languages.datatable') }}">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go Back
                        </x-href>
                  </x-header-simple>

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
        blade;
    }
}
