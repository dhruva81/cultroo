<?php

namespace App\Livewire\Users;

use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class UsersDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return User::query()
                ->where('user_type', 'user');
    }

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable(),
            TextColumn::make('subscription')
                ->formatStateUsing(fn () => 'Free Account'),
            TextColumn::make('created_at')
                ->sortable()
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
                            Forms\Components\Select::make('subscription')
                                ->options([
                                    'active' => 'Active Subscription',
                                    'inactive' => 'Free Account',
                                ])
                                ->placeholder('Subscription Status'),
                            Forms\Components\DatePicker::make('created_from')
                                ->placeholder('From Date'),
                            Forms\Components\DatePicker::make('created_until')
                                ->placeholder('Until Date'),
                        ])->columns(1),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
        ];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No Users found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new user';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Create user')
                ->url('#')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (User $record): string => route('users.show', $record);
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('export'),
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
                <x-header-simple title="Users" icon="heroicon-o-user-group" hideBorder />

                 {{ $this->table }}

            </div>
        blade;
    }
}
