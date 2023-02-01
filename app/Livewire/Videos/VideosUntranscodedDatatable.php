<?php

namespace App\Livewire\Videos;

use App\Jobs\StartAWSTranscodingVideoJob;
use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Language;
use App\Models\Series;
use App\Models\Tag;
use App\Models\Video;
use Closure;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class VideosUntranscodedDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Video::query()
            ->where('streamable_video_path', null)
            ;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('title')
                ->searchable(),
            Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                ->extraImgAttributes(['class' => 'w-16 h-9 object-cover rounded'])
                ->collection('thumbnail'),
            Tables\Columns\IconColumn::make('streamable_video_path')
                ->label('Streamable')
                ->options([
                    'heroicon-o-x-circle',
                    'heroicon-o-check-circle' => fn ($state): bool => $state !== null,
                    'heroicon-o-x-circle' => fn ($state): bool => $state === null,
                ])
                ->colors([
                    'danger',
                    'success' => fn ($state): bool => $state !== null,
                ]),
            Tables\Columns\BadgeColumn::make('status')
                ->enum([
                    Video::STATUS_PUBLISHED => 'Published',
                    Video::STATUS_DRAFT => 'Draft',
                    Video::STATUS_REVIEW => 'Reviewing',
                ])
                ->colors([
                    'success' => Video::STATUS_PUBLISHED,
                    'primary' => Video::STATUS_DRAFT,
                    'warning' => Video::STATUS_REVIEW,
                ]),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d M, Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('restartTranscoding')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-collection')
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        StartAWSTranscodingVideoJob::dispatch($record, true);
                    }
                    Notification::make()->title('Videos sent for transcoding!')->success()->send();
                })
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn(Video $video): string => route('videos.show', ['video' => $video]);
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Videos" icon="heroicon-o-video-camera" />

                 {{ $this->table }}
            </div>
        blade;
    }

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

//    protected function getTableFiltersFormColumns(): int
//    {
//        return 1;
//    }
//
//    protected function getTableFiltersLayout(): ?string
//    {
//        return $this->isTableInOtherPage()
//            ? Tables\Filters\Layout::Popover
//            : Tables\Filters\Layout::AboveContent;
//    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-video-camera';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No videos found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'No videos found. Please change your filters.';
    }

//    protected function getTableEmptyStateActions(): array
//    {
//        return [
//            Tables\Actions\Action::make('addVideosToCollection')
//                ->visible((bool)$this->collection)
//                ->label('Add shorts to the collection')
//                ->url(route('shorts.datatable', ['collection' => $this->collection]))
//                ->icon('heroicon-o-plus'),
//        ];
//    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }

    protected function isTableStriped(): bool
    {
        return true;
    }
}
