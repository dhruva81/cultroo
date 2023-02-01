<?php

namespace App\Livewire\Languages;

use App\Models\Language;
use App\Models\Section;
use Closure;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class LanguagesDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public ?Section $paramSection = null;

    public ?Section $section = null;

    public function mount()
    {
        $this->paramSection = request()->has('section') ? Section::findByUUID(request('section')) : null;

    }

    protected function getTableQuery(): Builder
    {
        return Language::query()
            ->withCount('videos')
            ->with([
                'sections',
            ])
            ->when($this->section, fn($query) => $query->whereRelation('sections', 'section_id', $this->section->id));
    }

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    public function getTableHeading(): string|Closure|null
    {
        return $this->section
            ? 'Languages'
            : null;
    }

    public function getTableHeaderActions(): ?array
    {
        return [
            Tables\Actions\Action::make('addLanguagesToSectionHeaderAction')
                ->visible((bool) $this->section)
                ->label('Add languages to the section')
                ->url(route('languages.datatable', ['section' => $this->section]))
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('videos_count')
                ->label('Videos')
                ->counts('videos'),
            Tables\Columns\BadgeColumn::make('status')
                ->enum([
                    1 => 'Active',
                    2 => 'Inactive',
                ])
                ->colors([
                    'success' => 1,
                    'warning' => 2,
                ]),
            Tables\Columns\TextColumn::make('created_at')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('d M, Y'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('edit')
                ->url(fn(Language $record): string => route('languages.edit', $record)),
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
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
        return 'No languages found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new languages';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->hidden((bool)$this->section)
                ->label('Create languages')
                ->url('#')
                ->icon('heroicon-o-plus'),
            Tables\Actions\Action::make('addLanguages')
                ->visible((bool)$this->section)
                ->label('Add languages')
                ->url($this->paramSection?->getAddToSectionLink())
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
            Tables\Actions\BulkAction::make('addToSection')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-color-swatch')
                ->modalButton('Add to section')
                ->modalHeading('Add language to section')
                ->modalSubheading('Select section to add language')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->sections()->syncWithoutDetaching($data['section_id']);
                    }
                    Notification::make()->title('Languages added successfully!')->success()->send();
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
                    Notification::make()->title('Languages removed successfully!')->success()->send();
                }),
        ];
    }

//    protected function getTableRecordUrlUsing(): Closure
//    {
//        return fn (Category $record): string => route('languages.show', $record);
//    }

    public function render()
    {
        return <<<'blade'
            <div>
                  @if(! $this->section)
                      <x-header-simple title="Languages" icon="heroicon-o-translate">
                           @if($paramSection)
                                <x-href href="{{ route('sections.show', $paramSection) }}" color="gray">
                                      <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                                      Back to Section
                                </x-href>
                           @endif

                            <x-href href="{{ route('languages.create') }}">
                                  <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                                  Add Language
                            </x-href>
                      </x-header-simple>
                  @endif

                 {{ $this->table }}

            </div>
        blade;
    }
}
