<?php

namespace App\Livewire\Plans;

use App\Models\Plan;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class PlanEdit extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public Plan $plan;

    public ?array $meta = null;

    public function mount(): void
    {
        $this->form->fill([
            'pg_plan_id' => $this->plan->pg_plan_id,
            'name' => $this->plan->name,
            'is_active' => $this->plan->is_active,
            'description' => $this->plan->description,
            'meta' => $this->plan->meta,
            'is_featured' => $this->plan->is_featured,
        ]);
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
                                    Forms\Components\Placeholder::make('pg')
                                        ->disableLabel()
                                        ->content(new HtmlString('<div class="text-sm text-blue-700 font-semibold">For payment gateway</div>')),
                                    Forms\Components\TextInput::make('name')
                                        ->dehydrated(false)
                                        ->disabled()
                                        ->required(),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Placeholder::make('mobile')
                                        ->disableLabel()
                                        ->content(new HtmlString('<div class="text-sm text-blue-700 font-semibold">For mobile screen</div>')),
                                    Forms\Components\TextInput::make('meta.label')
                                        ->label('Plan Name')
                                        ->hint('Free/Premium/Standard etc')
                                        ->required(),
                                    Forms\Components\TextInput::make('meta.hightlight_title')
                                        ->hint('Save 30% / Restricted Access')
                                        ->required(),
                                    Forms\Components\TextInput::make('meta.hightlight_subtitle')
                                        ->hint('US $120 billed annually')
                                        ->required(),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Repeater::make('meta.features')
                                        ->label('Plan Features')
                                        ->reactive()
                                        ->createItemButtonLabel('Add more')
                                        ->collapsible()
                                        ->columns(1)
                                        ->schema([
                                            Forms\Components\TextInput::make('item')
                                                ->disableLabel()
                                                ->prefixIcon('heroicon-o-check-circle')
                                                ->reactive()
                                                ->required(),
                                        ]),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Textarea::make('description')
                                        ->required(),
                                ]),
                        ])
                        ->columnSpan(2),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Toggle::make('is_featured')
                                        ->label('Is Featured Plan ?'),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('icon')
                                        ->preserveFilenames()
                                        ->visibility('public')
                                        ->collection('icon')
                                        ->disk('s3'),
                                ]),
                        ])
                        ->columnSpan(1),
                ]),

        ];
    }

    protected function getFormModel(): Plan
    {
        return $this->plan;
    }

    public function submit()
    {
        $this->plan->update($this->form->getState());

        Notification::make()
            ->title('Plan updated successfully!')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->success()
            ->send();

        return redirect()->route('plans.show', $this->plan);
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-7xl mx-auto my-8">

                  <x-header-simple title="Update Plan">
                        <x-href href="{{ route('plans.show', $plan) }}">
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
                                Update Plan
                        </x-button-loader>
                  </form>
            </div>
        blade;
    }
}
