<?php

namespace App\Livewire\Plans;

use App\Models\Plan;
use Filament\Forms;
use Filament\Notifications\Notification;
use Livewire\Component;

class PlanCreate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?string $pg_name = null;

    public ?string $pg_description = null;

    public ?int $billing_amount = null;

    public ?string $billing_period = null;

    public ?int $billing_interval = 1;

    public ?array $meta = null;

    public function mount(): void
    {
        $this->form->fill();
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
                                    Forms\Components\TextInput::make('pg_name')
                                        ->label('Name (PG)')
                                        ->required(),
                                    Forms\Components\Textarea::make('pg_description')
                                        ->label('Description (PG)'),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\TextInput::make('meta.name')
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
                        ])
                        ->columnSpan(2),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Toggle::make('is_free')
                                        ->reactive()
                                        ->label('Is Free Plan ?'),
                                    Forms\Components\TextInput::make('billing_amount')
                                        ->hidden(fn (\Closure $get) => $get('is_free') === true)
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\Hidden::make('billing_interval')
                                        ->required(),
                                    Forms\Components\Select::make('billing_period')
                                        ->options([
                                            Plan::BILLING_PERIOD_MONTHLY => 'Monthly',
                                            Plan::BILLING_PERIOD_YEARLY => 'Yearly',
                                        ])
                                        ->required(),
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

    protected function getFormModel(): string
    {
        return Plan::class;
    }

    public function submit()
    {
        $data = $this->form->getState();
        $plan = Plan::create($this->form->getState());
        $this->form->model($plan)->saveRelationships();

        ray($data);

        if (! $data['is_free']) {
            $response = Plan::createRazorpayPlan($this->form->getState());
            if ($response) {
                Notification::make()
                    ->title('Plan created successfully!')
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->success()
                    ->send();

                return redirect()->route('plans.datatable');
            }
        }

        Notification::make()
            ->title('Free plan created successfully!')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->success()
            ->send();
//        return redirect()->route('plans.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-7xl mx-auto my-8">

                  <x-header-simple title="Add new plan">
                        <x-href href="{{ route('plans.datatable') }}">
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
                                Create Plan
                        </x-button-loader>
                  </form>
            </div>
        blade;
    }
}
