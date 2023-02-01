<?php

namespace App\Livewire\Logs;

use App\Models\WatchHistory;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class WatchHistoriesDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return WatchHistory::query()
            ->with(['user', 'video'])
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.id')
                ->label('User ID')
                ->searchable(),
            Tables\Columns\TextColumn::make('user.email')
                ->label('Name')
                ->description(fn (WatchHistory $record) => $record->user?->name)
                ->searchable(),
            Tables\Columns\TextColumn::make('video.title')
                ->searchable()
                ->description(fn (WatchHistory $record) => $record->video?->series?->title),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Watched at')
                ->dateTime('d M Y, h:i A')
                ->since(),
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Watch History" icon="heroicon-o-clock">
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
