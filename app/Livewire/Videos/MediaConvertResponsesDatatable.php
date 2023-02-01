<?php

namespace App\Livewire\Videos;

use App\Models\Kollection;
use App\Models\MediaConvertResponse;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class MediaConvertResponsesDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return MediaConvertResponse::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('video_id')
                ->sortable()
                ->searchable()
                ->label('Video ID'),
            Tables\Columns\TextColumn::make('media_convert_job_id')
                ->searchable()
                ->label('Job ID'),
            Tables\Columns\TextColumn::make('data.status')
                ->label('Status'),
            Tables\Columns\TextColumn::make('created_at')
                ->sortable()
                ->dateTime(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('delete')
                ->action(fn (Collection $records) => $records->each->delete())
                ->deselectRecordsAfterCompletion()
                ->icon('heroicon-s-trash')
                ->color('danger')
                ->requiresConfirmation(),
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
//                            Kollection::STATUS_PUBLISHED => 'Published',
//                            Kollection::STATUS_DRAFT => 'Draft',
//                            Kollection::STATUS_REVIEW => 'Reviewing',
//                        ]),
//                ])
//                ->deselectRecordsAfterCompletion()
//                ->requiresConfirmation()
//                ->color('primary')
//                ->icon('heroicon-o-pencil-alt')
//                ->modalButton('Yes! update status'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
//            Tables\Actions\Action::make('edit')
//                ->url(fn (Kollection $record): string => route('collections.edit', $record)),
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
        return 'heroicon-o-video-camera';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No record found';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50, 100];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Transcoding Updates" icon="heroicon-o-color-swatch" />

                 {{ $this->table }}

            </div>
        blade;
    }

    protected function getDefaultTableSortColumn(): string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

}
