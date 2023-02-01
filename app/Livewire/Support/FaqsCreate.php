<?php

namespace App\Livewire\Support;

use App\Models\Avatar;
use App\Models\Faq;
use Filament\Forms;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class FaqsCreate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Repeater::make('questions')
                ->defaultItems(1)
                ->createItemButtonLabel('Add another question')
                ->disableLabel()
                ->schema([
                    Forms\Components\TextInput::make('question')
                        ->required(),
                    Forms\Components\Textarea::make('answer')
                        ->required(),
                    Forms\Components\Select::make('support_category_id')
                        ->options(\App\Models\SupportCategory::all()->pluck('title', 'id'))
                        ->required(),
                ])
        ];
    }

    public function submit()
    {
        foreach($this->form->getState()['questions'] as $question) {
            Faq::create([
                'question' => $question['question'],
                'answer' => $question['answer'],
                'support_category_id' => $question['support_category_id'],
            ]);
        }
        session()->flash('success', 'Question created successfully!');
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
