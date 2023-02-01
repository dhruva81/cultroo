<?php

namespace App\Livewire\Videos;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Language;
use App\Models\Section;
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

class VideosDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public ?Kollection $collection = null;

    public ?Kollection $paramCollection = null;

    public ?Section $paramSection = null;

    public ?Section $section = null;

    public ?Genre $genre = null;

    public ?Series $series = null;

    public ?Tag $tag = null;

    public ?int $languageId = null;

    public function mount()
    {
        $this->paramCollection = request()->has('collection') ? Kollection::findByUUID(request('collection')) : null;

        $this->paramSection = request()->has('section') ? Section::findByUUID(request('section')) : null;
    }

    protected function getTableQuery(): Builder
    {
        return Video::query()
            ->with([
                'genres',
                'language',
                'characters',
                'series',
                'collections',
                'sections'
            ])
            ->when(!$this->series, fn($query) => $query->whereNull('series_id') )
            ->when($this->tag, fn($query) => $query->whereRelation('tags', 'tag_id', $this->tag->id))
            ->when($this->series, fn($query) => $query->whereRelation('series', 'series_id', $this->series->id))
            ->when($this->languageId, fn($query) => $query->where('language_id', $this->languageId))
            ->when($this->collection, fn($query) => $query->whereRelation('collections', 'collection_id', $this->collection->id))
            ->when($this->section, fn ($query) => $query->whereRelation('sections', 'section_id', $this->section->id))
            ->when($this->genre, fn($query) => $query->whereRelation('genres', 'genre_id', $this->genre->id));
    }

    protected function isTableInOtherPage(): bool
    {
        return $this->collection || $this->genre || $this->tag || $this->series || $this->section;
    }

    public function getTableHeading(): string|Closure|null
    {
        $heading = null;
        if ($this->series) {
            $heading = 'Episodes';
        } elseif ($this->collection || $this->genre || $this->tag || $this->section) {
            $heading = 'Shorts';
        }
        return $heading;
    }

    public function getTableHeaderActions(): ?array
    {
        return [
            Tables\Actions\Action::make('addVideosToCollectionHeaderAction')
                ->visible((bool) $this->collection)
                ->label('Add shorts to the collection')
                ->url(route('shorts.datatable', ['collection' => $this->collection]))
                ->icon('heroicon-o-plus'),
            Tables\Actions\Action::make('videoUploaderHeaderAction')
                ->hidden(! $this->series)
                ->label('Upload new episodes')
                ->url($this->series ?
                    route('episodes.create', [
                        'series' => $this->series,
                        'language' => $this->languageId,
                    ]) : null
                )
                ->icon('heroicon-o-plus'),
            Tables\Actions\Action::make('addVideosToSectionHeaderAction')
                ->visible((bool)$this->section)
                ->label('Add shorts to the section')
                ->url(route('shorts.datatable', ['section' => $this->section]))
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->searchable()
                ->formatStateUsing(function (string $state, Video $record) {
                    if ($record->transcoding_status === 2) {
                        return new HtmlString('
                            <span class="inline-flex h-2 w-2 relative">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                            ' . $state);
                    }
                    return $state;
                })
                ->description(function (Video $record): string|null {
                    if ($this->series) return null;
                    return $record?->series?->title;
                }),
            Tables\Columns\TextColumn::make('language.name')
                ->toggleable(),
            Tables\Columns\TextColumn::make('episode_number')
                ->sortable()
                ->visible((bool)$this->series)
                ->label('Episode No.'),
            Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                ->extraImgAttributes(['class' => 'w-16 h-9 object-cover rounded'])
                ->collection('thumbnail'),
            Tables\Columns\ToggleColumn::make('is_featured')
                ->hidden((bool)$this->series)
                ->label('Featured'),
            Tables\Columns\ToggleColumn::make('is_free')
                ->label('Free'),
            Tables\Columns\IconColumn::make('streamable_video_path')
                ->toggleable(isToggledHiddenByDefault: true)
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
            Tables\Columns\TagsColumn::make('genres.name')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('Age Range')
                ->toggleable(isToggledHiddenByDefault: true)
                ->formatStateUsing(function ($record) {
                    if ($record->max_age) {
                        return $record->min_age . ' - ' . $record->max_age;
                    }

                    return $record->min_age . '+';
                }),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d M, Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return $this->collection || ($this->series && $this->languageId) ? [] : [
            Tables\Filters\Filter::make('is_featured')
                ->label('Featured Series')
                ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
            Tables\Filters\Filter::make('is_free')
                ->label('Free Shorts')
                ->query(fn (Builder $query): Builder => $query->where('is_free', true)),
            Tables\Filters\Filter::make('filters')
                ->form([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('language_id')
                                ->label('Language')
                                ->placeholder('Select Languages')
                                ->options(function() {
                                    if($this->series && $this->series->getLanguages()->count() > 0)
                                    {
                                        return $this->series->getLanguages()->pluck('name', 'id');
                                    }
                                    return Language::pluck('name', 'id');
                                }),
                            Forms\Components\Select::make('episode_number')
                                ->label('Episode No.')
                                ->visible((bool) $this->series)
                                ->placeholder('Select Episode No.')
                                ->options($this->series ? $this->series?->getEpisodeNumbers() : []),
                            Forms\Components\Select::make('status')
                                ->options([
                                    Video::STATUS_PUBLISHED => 'Published',
                                    Video::STATUS_DRAFT => 'Draft',
                                    Video::STATUS_REVIEW => 'Review',
                                ])
                                ->label('Status')
                                ->placeholder('Select Status'),
                        ])->columns($this->isTableInOtherPage() ? 1 : 1),
                ])
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['language_id'] ?? null) {
                        $indicators['language_id'] = 'Language - ' . Language::find($data['language_id'])?->name;
                    }

                    if ($data['status'] ?? null) {
                        $indicators['status'] = 'Status - ' . Video::getVideoStatus($data['status']);
                    }

                    if ($data['episode_number'] ?? null) {
                        $indicators['episode_number'] = 'Episode Number - ' . $data['episode_number'];
                    }

                    return $indicators;
                })
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['language_id'],
                            fn(Builder $query, $term): Builder => $query->where('language_id', $term),
                        )
                        ->when(
                            $data['status'],
                            fn(Builder $query, $term): Builder => $query->where('status', $term),
                        )
                        ->when(
                            $data['episode_number'],
                            fn(Builder $query, $term): Builder => $query->where('episode_number', $term),
                        );
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-s-eye')
                    ->url(fn(Video $record): string => route('videos.show', $record)),
                Tables\Actions\Action::make('quickEdit')
                    ->label('Quick Edit')
                    ->icon('heroicon-s-pencil-alt')
                    ->mountUsing(fn(Forms\ComponentContainer $form, Video $record) => $form->fill([
                        'title' => $record->title,
                        'description' => $record->description,
                        'language_id' => $record->language_id,
                    ]))
                    ->action(function (Video $record, array $data): void {
                        $record->title = $data['title'];
                        $record->description = $data['description'];
                        $record->language_id = $data['language_id'];
                        $record->save();
                    })
                    ->modalWidth('2xl')
                    ->form([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->avatar()
                            ->visibility('public')
                            ->collection('thumbnail')
                            ->preserveFilenames()
                            ->disk('s3'),
                        Forms\Components\TextInput::make('title')
                            ->label('Video Title')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2),
                        Forms\Components\Grid::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('language_id')
                                    ->options(Language::pluck('name', 'id'))
                                    ->label('Language')
                                    ->required(),
                                Forms\Components\Select::make('genres')
                                    ->multiple()
                                    ->label('Category')
                                    ->visible(function(Video $record){
                                        return $record->series_id === null;
                                    })
                                    ->relationship('genres', 'name')
                                    ->options(Genre::pluck('name', 'id'))
                                    ->placeholder('Select Category')
                                    ->searchable(),
                            ]),
                        Forms\Components\Grid::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('characters')
                                    ->multiple()
                                    ->relationship('characters', 'name')
                                    ->options(Character::pluck('name', 'id'))
                                    ->placeholder('Select characters')
                                    ->searchable(),
                                Forms\Components\Select::make('tags')
                                    ->multiple()
                                    ->relationship('tags', 'name')
                                    ->options(Tag::pluck('name', 'id'))
                                    ->placeholder('Select tags')
                                    ->searchable(),
                            ]),

                    ]),
                Tables\Actions\Action::make('edit')
                    ->icon('heroicon-s-pencil-alt')
                    ->url(fn(Video $record): string => route('videos.edit', $record)),
                Tables\Actions\Action::make('delete')
                    ->action(fn(Video $record) => $record->delete())
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Video')
                    ->modalSubheading('Are you sure you\'d like to delete this Video? This cannot be undone.')
                    ->modalButton('Yes, delete')
            ]),
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
                            Video::STATUS_PUBLISHED => 'Published',
                            Video::STATUS_DRAFT => 'Draft',
                            Video::STATUS_REVIEW => 'Reviewing',
                        ]),
                ])
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-pencil-alt')
                ->modalButton('Yes! update status'),
            Tables\Actions\BulkAction::make('addToSeries')
                ->hidden((bool)$this->series)
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-collection')
                ->modalButton('Add to series')
                ->modalHeading('Add videos to series')
                ->modalSubheading('Select series to add videos to')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->update(['series_id' => $data['series_id']]);
                    }
                    Notification::make()->title('Videos added successfully!')->success()->send();
                })
                ->form([
                    Forms\Components\Select::make('series_id')
                        ->required()
                        ->searchable()
                        ->placeholder('Select series')
                        ->disableLabel('Select series')
                        ->options(Series::pluck('title', 'id')),
                ]),
            Tables\Actions\BulkAction::make('addToCollection')
                ->hidden((bool)$this->collection)
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-color-swatch')
                ->modalButton('Add to collection')
                ->modalHeading('Add videos to collection')
                ->modalSubheading('Select collection to add videos to')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->collections()->syncWithoutDetaching($data['collection_id']);
                    }
                    Notification::make()->title('Videos added successfully!')->success()->send();
                })
                ->form([
                    Forms\Components\Select::make('collection_id')
                        ->required()
                        ->searchable()
                        ->default($this->paramCollection?->id)
                        ->placeholder('Select collection')
                        ->disableLabel('Select collection')
                        ->options(Kollection::pluck('name', 'id')),
                ]),
            Tables\Actions\BulkAction::make('removeFromCollection')
                ->hidden(!$this->collection)
                ->icon('heroicon-o-minus')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        $record->collections()->detach($this->collection);
                    }
                    Notification::make()->title('Videos removed successfully!')->success()->send();
                }),
            Tables\Actions\BulkAction::make('addToSection')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-color-swatch')
                ->modalButton('Add to section')
                ->modalHeading('Add series to section')
                ->modalSubheading('Select section to add series to')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->sections()->syncWithoutDetaching($data['section_id']);
                    }
                    Notification::make()->title('Videos added successfully!')->success()->send();
                })
                ->form([
                    Forms\Components\Select::make('section_id')
                        ->required()
                        ->searchable()
                        ->default($this->paramSection?->id)
                        ->placeholder('Select section')
                        ->disableLabel('Select section')
                        ->options(Section::pluck('name', 'id')),
                ]),
            Tables\Actions\BulkAction::make('removeFromSection')
                ->hidden(! $this->section)
                ->icon('heroicon-o-minus')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        $record->sections()->detach($this->section);
                    }
                    Notification::make()->title('Videos removed successfully!')->success()->send();
                }),
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

                    @if(!$collection && !$genre && !$series && !$tag && !$section)
                        <x-header-simple title="Shorts" icon="heroicon-o-video-camera">

                        @if($paramCollection)
                            <x-href href="{{ route('collections.show', $paramCollection) }}" color="gray">
                                  <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                                  Back to collection
                            </x-href>
                        @endif

                        <x-href href="{{ route('shorts.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              New Short
                        </x-href>

                  </x-header-simple>
                   @endif

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
        if ($this->series) {
            return 'No episode found!';
        }

        return 'No shorts found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if ($this->tag) {
            return 'No shorts found for this tag';
        }
        if ($this->collection) {
            return 'No shorts found in this collection';
        }
        if ($this->genre) {
            return 'No shorts found in this category';
        }

        return 'Please change your filters.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('addVideosToCollection')
                ->visible((bool)$this->collection)
                ->label('Add shorts to the collection')
                ->url(route('shorts.datatable', ['collection' => $this->collection]))
                ->icon('heroicon-o-plus'),
            Tables\Actions\Action::make('addVideosToSection')
                ->visible((bool)$this->section)
                ->label('Add shorts to the section')
                ->url(route('shorts.datatable', ['section' => $this->section]))
                ->icon('heroicon-o-plus'),
//            Tables\Actions\Action::make('create')
//                ->hidden((bool)$this->collection)
//                ->label('Create Video')
//                ->url(route('videos.create'))
//                ->icon('heroicon-o-plus'),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return !$this->collection;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }

    protected function getDefaultTableSortColumn(): string
    {
        return $this->series ? 'episode_number' : 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return $this->series ? 'asc' : 'desc';
    }

    protected function getTableReorderColumn(): ?string
    {
        return $this->series && $this->languageId ? 'episode_number' : null;
    }

    protected function isTableStriped(): bool
    {
        return true;
    }
}
