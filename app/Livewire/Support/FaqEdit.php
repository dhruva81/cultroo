<?php

namespace App\Livewire\Support;

use App\Models\Avatar;
use App\Models\Faq;
use Filament\Forms;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class FaqEdit extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Faq $faq = null;

    public function mount(): void
    {
        $this->form->fill([
            'question' => $this->faq->question,
            'answer' => $this->faq->answer,
            'support_category_id' => $this->faq->support_category_id,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('question')
                ->required(),
            Forms\Components\Textarea::make('answer')
                ->required(),
            Forms\Components\Select::make('support_category_id')
                ->options(\App\Models\SupportCategory::all()->pluck('title', 'id'))
                ->required(),
        ];
    }

    public function submit()
    {
        $this->faq->update($this->form->getState());
        session()->flash('success', 'Question updated successfully!');
        return redirect()->route('support-faqs.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="Support Questions">
                        <x-href href="{{route('support-faqs.datatable')  }}">
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
