<?php

namespace App\Livewire\Sections;

use App\Models\Section;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SectionsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Section::query();
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
            Tables\Columns\TextColumn::make('model'),
            Tables\Columns\SpatieMediaLibraryImageColumn::make('icon')
                ->extraImgAttributes(['class' => 'w-10 h-10'])
                ->collection('icon'),
            Tables\Columns\SpatieMediaLibraryImageColumn::make('cover_image')
                ->extraImgAttributes(['class' => 'w-16 h-9 object-cover rounded'])
                ->collection('cover_image'),
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
                ->dateTime('d M, Y'),
        ];
    }

//    protected function getTableBulkActions(): array
//    {
//        return [
//            Tables\Actions\BulkAction::make('updateStatus')
//                ->action(function (Collection $records, array $data): void {
//                    foreach ($records as $record) {
//                        $record->status = $data['status'];
//                        $record->save();
//                    }
//                })
//                ->form([
//                    Forms\Components\Select::make('status')
//                        ->required()
//                        ->placeholder('Select status')
//                        ->disableLabel()
//                        ->options([
//                            Section::STATUS_PUBLISHED => 'Published',
//                            Section::STATUS_DRAFT => 'Draft',
//                            Section::STATUS_REVIEW => 'Reviewing',
//                        ]),
//                ])
//                ->deselectRecordsAfterCompletion()
//                ->requiresConfirmation()
//                ->color('primary')
//                ->icon('heroicon-o-pencil-alt')
//                ->modalButton('Yes! update status'),
//        ];
//    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\DeleteAction::make('delete'),
        ];
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn (Section $record): string => route('sections.show', $record);
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
        return 'No sections found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new section.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Create section')
                ->url(route('sections.create'))
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
                  <x-header-simple title="Sections" icon="heroicon-o-server">
                        <x-href href="{{ route('sections.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              Create section
                        </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
