<?php

namespace App\Livewire\Collections;

use App\Models\Kollection;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CollectionsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Kollection::query();
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
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
            Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                ->extraImgAttributes(['class' => 'w-16 h-9 object-cover rounded'])
                ->collection('thumbnail'),
            Tables\Columns\BadgeColumn::make('status')
                ->enum([
                    1 => 'Published',
                    2 => 'Draft',
                    3 => 'Reviewing',
                ])
                ->colors([
                    'success' => 1,
                    'primary' => 2,
                    'warning' => 3,
                ]),
            Tables\Columns\TextColumn::make('created_at')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('d M, Y')
                ->extraAttributes(['class' => 'text-sm']),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('updateStatus')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->status = $data['status'];
                        $record->save();
                    }
                })
                ->form([
                    Forms\Components\Select::make('status')
                        ->required()
                        ->placeholder('Select status')
                        ->disableLabel()
                        ->options([
                            Kollection::STATUS_PUBLISHED => 'Published',
                            Kollection::STATUS_DRAFT => 'Draft',
                            Kollection::STATUS_REVIEW => 'Reviewing',
                        ]),
                ])
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-pencil-alt')
                ->modalButton('Yes! update status'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('edit')
                ->url(fn (Kollection $record): string => route('collections.edit', $record)),
        ];
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn (Kollection $record): string => route('collections.show', $record);
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
        return 'No collections found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new collection';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Create collection')
                ->url(route('collections.create'))
                ->icon('heroicon-o-plus'),
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
                  <x-header-simple title="Collections" icon="heroicon-o-color-swatch">
                        <x-href href="{{ route('collections.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              Create Collection
                        </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
