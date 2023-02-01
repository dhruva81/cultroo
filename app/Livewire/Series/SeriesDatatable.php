<?php

namespace App\Livewire\Series;

use App\Models\Genre;
use App\Models\Kollection;
use App\Models\Language;
use App\Models\Section;
use App\Models\Series;
use App\Models\Tag;
use Closure;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class SeriesDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public ?Kollection $collection = null;

    public ?Kollection $paramCollection = null;

    public ?Section $paramSection = null;

    public ?Section $section = null;

    public ?Genre $genre = null;

    public ?Tag $tag = null;

    public function mount()
    {
        $this->paramCollection = request()->has('collection') ? Kollection::findByUUID(request('collection')) : null;

        $this->paramSection = request()->has('section') ? Section::findByUUID(request('section')) : null;

    }

    protected function isTableInOtherPage(): bool
    {
        return $this->collection || $this->genre || $this->tag || $this->section;
    }

    public function getTableHeading(): string|Closure|null
    {
        return $this->isTableInOtherPage()
            ? 'Series'
            : null;
    }

    public function getTableHeaderActions(): ?array
    {
        return [
            Tables\Actions\Action::make('addSeriesToCollectionHeaderAction')
                ->visible((bool) $this->collection)
                ->label('Add series to the collection')
                ->url(route('series.datatable', ['collection' => $this->collection]))
                ->icon('heroicon-o-plus'),
            Tables\Actions\Action::make('addSeriesToSectionHeaderAction')
                ->visible((bool) $this->section)
                ->label('Add series to the section')
                ->url(route('series.datatable', ['collection' => $this->section]))
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Series::query()
            ->with([
                'videos',
                'collections',
                'sections'
            ])
            ->when($this->tag, fn ($query) => $query->whereRelation('tags', 'tag_id', $this->tag->id))
            ->when($this->collection, fn ($query) => $query->whereRelation('collections', 'collection_id', $this->collection->id))
            ->when($this->section, fn ($query) => $query->whereRelation('sections', 'section_id', $this->section->id))
            ->when($this->genre, fn ($query) => $query->whereRelation('genres', 'genre_id', $this->genre->id));
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
            Tables\Columns\TextColumn::make('title')
                ->label('Series Title')
                ->searchable(),
            Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                ->extraImgAttributes(['class' => 'w-16 h-9 object-cover rounded'])
                ->collection('thumbnail'),
            Tables\Columns\TextColumn::make('videos_count')
                ->counts('videos')
                ->label('Episodes'),
            Tables\Columns\ToggleColumn::make('is_featured')
                ->label('Featured'),
            Tables\Columns\BadgeColumn::make('badge_status')
                ->enum([
                    'published' => 'Published',
                    'published_not_visible' => 'No published episodes',
                    'draft' => 'Draft',
                    'review' => 'Reviewing',
                ])
                ->colors([
                    'success' => 'published',
                    'danger' => 'published_not_visible',
                    'warning' => 'review',
                    'draft' => 'secondary',
                ])
                ->icons([
                    'heroicon-o-check' => 'published',
                    'heroicon-o-exclamation-circle' => 'published_not_visible',
                ]),
            Tables\Columns\TextColumn::make('created_at')
                ->sortable()
                ->dateTime('d M, Y')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getTableFilters(): array
    {
        if ($this->collection) {
            return [];
        }

        return [
            Tables\Filters\Filter::make('is_featured')
                ->label('Featured Series')
                ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
            Tables\Filters\Filter::make('filters')
                ->form([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('languages')
                                ->options(Language::pluck('name', 'id'))
                                ->label('Language')
                                ->placeholder('Select Languages'),
                            Forms\Components\Select::make('status')
                                ->options([
                                    Series::STATUS_PUBLISHED => 'Published',
                                    Series::STATUS_DRAFT => 'Draft',
                                    Series::STATUS_REVIEW => 'Review',
                                ])
                                ->label('Status')
                                ->placeholder('Select Status'),
                        ])->columns($this->isTableInOtherPage() ? 1 : 1),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['languages'],
                            fn (Builder $query, $term): Builder => $query->whereRelation('videos', 'language_id', $term),
                        )
                        ->when(
                            $data['status'],
                            fn (Builder $query, $term): Builder => $query->where('status', $term),
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
                    ->url(fn (Series $record): string => route('series.show', $record)),
                Tables\Actions\Action::make('quickEdit')
                    ->label('Quick Edit')
                    ->icon('heroicon-s-pencil-alt')
                    ->mountUsing(fn (Forms\ComponentContainer $form, Series $record) => $form->fill([
                        'title' => $record->title,
                        'short_description' => $record->short_description,
                    ]))
                    ->action(function (Series $record, array $data): void {
                        $record->title = $data['title'];
                        $record->description = $data['description'];
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
                            ->label('Series Title')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2),
                    ]),
                Tables\Actions\Action::make('edit')
                    ->icon('heroicon-s-pencil-alt')
                    ->url(fn (Series $record): string => route('series.show', ['series' => $record, 'tab' => 'edit'])),
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
                            Series::STATUS_PUBLISHED => 'Published',
                            Series::STATUS_DRAFT => 'Draft',
                            Series::STATUS_REVIEW => 'Reviewing',
                        ]),
                ])
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-pencil-alt')
                ->modalButton('Yes! update status'),
            Tables\Actions\BulkAction::make('addToCollection')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-color-swatch')
                ->modalButton('Add to collection')
                ->modalHeading('Add series to collection')
                ->modalSubheading('Select collection to add series to')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->collections()->syncWithoutDetaching($data['collection_id']);
                    }
                    Notification::make()->title('Series added successfully!')->success()->send();
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
                ->hidden(! $this->collection)
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
                    Notification::make()->title('Series added successfully!')->success()->send();
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
                    Notification::make()->title('Series removed successfully!')->success()->send();
                }),
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>

                   @if(!$collection && !$genre && !$tag && !$section)
                  <x-header-simple title="Series" icon="heroicon-o-collection">

                        @if($paramCollection)
                            <x-href href="{{ route('collections.show', $paramCollection) }}" color="gray">
                                  <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                                  Back to collection
                            </x-href>
                        @endif

                        @if(!$collection)
                            <x-href href="{{ route('series.create') }}">
                                  <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                                  Create Series
                            </x-href>
                        @endif
                  </x-header-simple>
                  @endif

                 {{ $this->table }}

            </div>
        blade;
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-collection';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No Series found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if($this->tag) {
            return 'No series found in this tag';
        }
        if($this->collection) {
            return 'No series found in this collection';
        }
        if($this->genre) {
            return 'No series found in this category';
        }

        return 'Please change your filters.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('addSeriesToCollection')
                ->visible((bool) $this->collection)
                ->label('Add series to the collection')
                ->url(route('series.datatable', ['collection' => $this->collection]))
                ->icon('heroicon-o-plus'),
            Tables\Actions\Action::make('addSeriesToSection')
                ->visible((bool) $this->section)
                ->label('Add series to the section')
                ->url(route('series.datatable', ['section' => $this->section]))
                ->icon('heroicon-o-plus'),
//            Tables\Actions\Action::make('create')
//                ->hidden((bool) $this->collection)
//                ->label('Create series')
//                ->url(route('series.create'))
//                ->icon('heroicon-o-plus'),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return ! $this->collection;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50, 100];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Series $record): string => route('series.show', $record);
    }

}
