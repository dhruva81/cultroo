<?php

namespace App\Livewire\Logs;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Jenssegers\Agent\Agent;
use Livewire\Component;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class AuthenticationLogsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return AuthenticationLog::query()
            ->latest('login_at');
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('authenticatable.name')
                ->label('Name')
                ->searchable(),
            TextColumn::make('ip_address')
                ->label('IP Address'),
            TextColumn::make('user_agent')
                ->label('Device')
                ->formatStateUsing(function ($state) {
                    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($state));

                    return $agent->platform().' - '.$agent->browser();
                })
                ->searchable(),
            TextColumn::make('location')
                ->label('Location')
                ->formatStateUsing(function ($state) {
                    return '-';
                })
                ->searchable(),
            TextColumn::make('login_at')
                ->dateTime()
                ->since(),
            Tables\Columns\BooleanColumn::make('login_successful'),
            TextColumn::make('logout_at')
                ->dateTime('d M, Y'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('filters')
                ->form([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('device')
                                ->options([
                                    'mobile' => 'Mobile',
                                    'laptop' => 'Laptop',
                                ])
                                ->label('Device'),
                            Forms\Components\DatePicker::make('created_from')
                                ->placeholder('From Date')
                                ->label('Login From'),
                            Forms\Components\DatePicker::make('created_until')
                                ->placeholder('Until Date')
                                ->label('Login Until'),
                        ])
                        ->columns(1),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('login_at', '<=', $date),
                        );
                }),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50, 100];
    }

    public function render()
    {
        return <<<'blade'
            <div>

                  <x-header-simple title="Authentication Logs"  icon="heroicon-o-flag" subtitle="Authentication logs of admin panel users" >
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
