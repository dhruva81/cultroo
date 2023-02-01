<?php

namespace App\Livewire\Support;

use App\Models\SupportCategory;
use Filament\Forms;
use Livewire\Component;

class SupportCategoryCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?SupportCategory $support_category = null;

    public ?string $title = null;

    public ?string $description = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->support_category ?
                [
                    'title' => $this->support_category->title,
                    'description' => $this->support_category->description,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('Title')
                ->unique('support_categories', 'title', ignorable: $this->support_category)
                ->dehydrateStateUsing(fn ($state) => ucwords($state))
                ->required(),
            Forms\Components\Checkbox::make('is_published')
                ->label('Published')
                ->default(false),
            Forms\Components\Textarea::make('description'),
        ];
    }

    protected function getFormModel(): SupportCategory|string
    {
        return $this->support_category ? $this->support_category : SupportCategory::class;
    }

    public function submit()
    {

        if ($this->support_category) {
            $this->support_category->update($this->form->getState());
            session()->flash('success', 'Support Category updated successfully!');
            return redirect()->route('support-categories.datatable');
        }

        $support_category = SupportCategory::create($this->form->getState());

        session()->flash('success', 'Support Category created successfully!');
        return redirect()->route('support-categories.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="{{ $this->support_category ? 'Edit Support Category' : 'Create Support Category' }}">
                        <x-href href="{{route('support-categories.datatable')  }}">
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
