<?php

namespace App\Livewire\Support;

use App\Models\SupportCategory;
use App\Models\Kollection;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class SupportCategoriesDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return SupportCategory::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->description(fn (SupportCategory $record) => $record->description)
                ->searchable()
                ->sortable(),
            Tables\Columns\IconColumn::make('is_published')
                ->boolean(),
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
//                            SupportCategory::STATUS_PUBLISHED => 'Published',
//                            SupportCategory::STATUS_DRAFT => 'Draft',
//                            SupportCategory::STATUS_REVIEW => 'Reviewing',
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
            Tables\Actions\Action::make('edit')
                ->url(fn (SupportCategory $record): string => route('support-categories.edit', $record)),
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
        return 'No categories found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new category.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Create genre')
                ->url('#')
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
                  <x-header-simple title="Support Categories" icon="heroicon-o-support">
                        <x-href href="{{ route('support-categories.datatable') }}" color="white">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go Back
                        </x-href>
                        <x-href href="{{ route('support-categories.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              Create category
                        </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
