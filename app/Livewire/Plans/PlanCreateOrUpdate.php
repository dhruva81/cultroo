<?php

namespace App\Livewire\Plans;

use App\Models\Plan;
use Filament\Forms;
use Filament\Notifications\Notification;
use Livewire\Component;
use Razorpay\Api\Api as RazorpayApi;

class PlanCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Plan $plan = null;

    public ?string $pg_name = null;

    public ?string $pg_description = null;

    public ?int $pg_billing_amount = null;

    public ?string $pg_billing_period = null;

    public ?array $meta = null;

    public ?bool $is_active = false;

    public ?bool $is_featured = false;

    public function mount(): void
    {
        $this->form->fill(
            $this->plan ?
                [
                    'pg_name' => $this->plan->pg_name,
                    'pg_description' => $this->plan->pg_description,
                    'pg_plan_id' => $this->plan->pg_plan_id,
                    'pg_billing_amount' => $this->plan->pg_billing_amount,
                    'pg_billing_period' => $this->plan->pg_billing_period,
                    'is_active' => $this->plan->is_active,
                    'is_featured' => $this->plan->is_featured,
                    'meta' => $this->plan->meta,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Placeholder::make('Payment Gateway')
                ->disableLabel()
                ->content('Following section required for payment gateway. It can not be updated later.'),
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
                                        ->label('Name (for payment gateway)')
                                        ->disabled((bool) $this->plan)
                                        ->dehydrated(! $this->plan)
                                        ->required(),
                                    Forms\Components\Textarea::make('pg_description')
                                        ->dehydrated(! $this->plan)
                                        ->disabled((bool) $this->plan)
                                        ->rows(3)
                                        ->label('Description (for payment gateway)'),
                                ]),
                        ])
                        ->columnSpan(2),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\TextInput::make('pg_billing_amount')
                                        ->label('Billing amount')
                                        ->dehydrated(! $this->plan)
                                        ->disabled((bool) $this->plan)
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\Select::make('pg_billing_period')
                                        ->label('Billing period')
                                        ->dehydrated(! $this->plan)
                                        ->disabled((bool) $this->plan)
                                        ->options([
                                            Plan::BILLING_PERIOD_MONTHLY => 'Monthly',
                                            Plan::BILLING_PERIOD_YEARLY => 'Yearly',
                                        ])
                                        ->required(),
                                ]),
                        ])
                        ->columnSpan(1),
                ]),
            Forms\Components\Placeholder::make('mobile_screen')
                ->disableLabel()
                ->content('Following section will be displayed on mobile screen.'),
            Forms\Components\Grid::make([
                'default' => 1,
                'sm' => 3,
            ])
                ->schema([
                    Forms\Components\Group::make()
                        ->schema([
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
                                    Forms\Components\Textarea::make('meta.description')
                                        ->rows(3),
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
                                    Forms\Components\Toggle::make('is_featured')
                                        ->label('Is Featured Plan ?'),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Is Active Plan ?'),
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

    protected function getFormModel(): Plan|string
    {
        return $this->plan ? $this->plan : Plan::class;
    }

    public function submit()
    {
        $data = $this->form->getState();

        if ($this->plan) {
            $this->plan->update($this->form->getState());
            $this->form->model($this->plan)->saveRelationships();
            Notification::make()
                ->title('Plan updated successfully!')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->success()
                ->send();

            return redirect()->route('plans.show', ['plan' => $this->plan]);
        }

        $api = new RazorpayApi(config('app.razorpay_key'), config('app.razorpay_secret'));

        try {
            $response = $api->plan->create([
                'period' => $data['pg_billing_period'],
                'interval' => 1,
                'item' => [
                    'name' => $data['pg_name'],
                    'description' => $data['pg_description'],
                    'amount' => $data['pg_billing_amount'] * 100,
                    'currency' => 'INR',
                ],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->icon('heroicon-o-x')
                ->iconColor('danger')
                ->danger()
                ->send();

            return false;
        }

        $plan = Plan::create([
            'pg_name' => $data['pg_name'],
            'pg_description' => $data['pg_description'],
            'pg_billing_amount' => $data['pg_billing_amount'],
            'pg_billing_interval' => 1,
            'pg_billing_period' => $data['pg_billing_period'],
            'pg_plan_id' => $response['id'],
            'payment_gateway' => Plan::PAYMENT_GATEWAY_RAZORPAY,
            'meta' => $data['meta'],
        ]);

        ray($response);
        ray($plan);

        $this->form->model($plan)->saveRelationships();

        Notification::make()
            ->title('Plan created successfully!')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->success()
            ->send();

        return redirect()->route('plans.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-7xl mx-auto my-8">

                  <x-header-simple title="{{ $this->plan ? 'Edit Plan' : 'Add new plan' }}">
                        <x-href href="{{ $this->plan ? route('plans.show', $plan) : route('plans.datatable') }}">
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
