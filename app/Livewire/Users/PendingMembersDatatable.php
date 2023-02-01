<?php

namespace App\Livewire\Users;

use App\Models\PendingUser;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class PendingMembersDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return PendingUser::query()
            ->where('user_type', 'admin');
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
            TextColumn::make('email')
                ->searchable(),
            TextColumn::make('created_at')
                ->sortable()
                ->dateTime('d M, Y'),
        ];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No pending admin user found';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Invite Admin Members')
                ->url(route('members.invite'))
                ->icon('heroicon-o-mail'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50, 100];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('Cancel Invitation')
                ->action(fn (Collection $records) => $records->each->delete())
                ->deselectRecordsAfterCompletion()
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),

        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <x-header-simple title="Pending Members" subtitle="Invited pending members, not registered yet." icon="heroicon-o-clock" hideBorder>
                     <x-href href="{{ route('members.datatable') }}">
                          <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                          Admin Members
                    </x-href>
                    <x-href href="{{ route('members.invite') }}">
                          <x-heroicon-o-mail class="h-5 w-5 -ml-2 mr-2" />
                          Invite Admin Member
                    </x-href>
                </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
