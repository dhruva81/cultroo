<?php

namespace App\Livewire\Logs;

use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Component;

class AccessTokensDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return PersonalAccessToken::query()
            ->latest('last_used_at');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tokenable.id')
                ->label('User ID')
                ->searchable(),
            Tables\Columns\TextColumn::make('tokenable.email')
                ->description(fn (PersonalAccessToken $record) => $record->tokenable?->name)
                ->label('User')
                ->searchable(),
            Tables\Columns\TextColumn::make('name')
                ->label('Device')
                ->searchable(),
            Tables\Columns\TextColumn::make('last_used_at')
                ->label('Last Used App')
                ->sortable()
                ->dateTime()
                ->since(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Token Created at')
                ->dateTime(),
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Access Tokens" icon="heroicon-o-finger-print" subtitle="Tokens usage of app users">
                     <x-href href="{{ route('dashboard') }}" color="white">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go to Dashboard
                            </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }


    protected function getDefaultTableSortColumn(): string
    {
        return 'last_used_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
