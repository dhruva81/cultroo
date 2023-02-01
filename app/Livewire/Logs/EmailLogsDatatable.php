<?php

namespace App\Livewire\Logs;

use App\Models\EmailLog;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class EmailLogsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return EmailLog::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->label('Date')
                ->sortable()
                ->datetime(),
            Tables\Columns\TextColumn::make('from')
                ->label('From')
                ->searchable(),
            Tables\Columns\TextColumn::make('to')
                ->label('To')
                ->searchable(),
            Tables\Columns\TextColumn::make('subject')
                ->label('Email Subject'),
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Email Logs" icon="heroicon-o-mail" subtitle="All outgoing email logs">
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
        return 'date';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

}
