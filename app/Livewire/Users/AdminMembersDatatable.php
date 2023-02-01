<?php

namespace App\Livewire\Users;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class AdminMembersDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->where('user_type', 'admin')
            ->orWhere('user_type', 'super_admin');
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
            TextColumn::make('user_type')
                ->label('User Type')
                ->formatStateUsing(fn ($state) => ucfirst($state)),
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
                ->label('Create student')
                ->url('#')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50, 100];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('export'),
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <x-header-simple title="Admin Staff Members" icon="heroicon-o-users" hideBorder>
                  <x-href href="{{ route('members.pending.datatable') }}">
                          <x-heroicon-o-clock class="h-5 w-5 -ml-2 mr-2" />
                          Pending Members
                    </x-href>
                    <x-href href="{{ route('members.invite') }}">
                          <x-heroicon-o-mail class="h-5 w-5 -ml-2 mr-2" />
                          Invite Staff Member
                    </x-href>
                </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
