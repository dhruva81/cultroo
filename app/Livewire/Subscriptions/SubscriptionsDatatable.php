<?php

namespace App\Livewire\Subscriptions;

use App\Models\Subscription;
use Closure;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SubscriptionsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Subscription::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name'),
            Tables\Columns\TextColumn::make('pg_subscription_id'),
            Tables\Columns\TextColumn::make('created_at')
                ->date(),
        ];
    }

//    protected function getTableRecordUrlUsing(): Closure
//    {
//        return fn (Subscription $record): string => route('plans.show', $record);
//    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Subscriptions" icon="heroicon-o-collection" subtitle="A list of all subscriptions." />

                 {{ $this->table }}

            </div>
        blade;
    }
}
