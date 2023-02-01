<?php

namespace App\Livewire\Plans;

use App\Models\Plan;
use Cknow\Money\Money;
use Closure;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PlansDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Plan::query();
    }

    protected function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('pg_name')
                ->label('Name')
                ->description(fn (Plan $record) => $record->meta['name'])
                ->searchable(),
            Tables\Columns\TextColumn::make('pg_billing_amount')
                ->formatStateUsing(fn (Plan $record) => Money::INR($record->pg_billing_amount))
                ->label('Amount'),
            Tables\Columns\TextColumn::make('pg_billing_period')
                ->label('Period'),
            Tables\Columns\BooleanColumn::make('is_active'),
            Tables\Columns\TextColumn::make('created_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->date(),
            Tables\Columns\TextColumn::make('pg_plan_id')
                ->label('Plan ID')
                ->description('Razorpay')
                ->searchable(),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Plan $record): string => route('plans.show', $record);
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Plans" icon="heroicon-o-collection" subtitle="A list of all plans.">
                        <x-href href="{{ route('plans.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              Create Plan
                        </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
