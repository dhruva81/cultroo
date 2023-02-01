<?php

namespace App\Livewire\Logs;

use App\Models\Activity;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ActivityLogsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Activity::query()
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('activity_description')
                ->label('Activity')
                ->html(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created at')
                ->dateTime()
                ->since(),
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Activity Logs" icon="heroicon-o-clock" subtitle="Activity logs of app users">
                     <x-href href="{{ route('dashboard') }}" color="white">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go to Dashboard
                        </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
