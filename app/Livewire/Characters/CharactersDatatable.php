<?php

namespace App\Livewire\Characters;

use App\Models\Character;
use App\Models\Kollection;
use App\Models\Section;
use App\Models\Series;
use Closure;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CharactersDatatable extends Component implements Tables\Contracts\HasTable
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
        return Character::withCount('videos')
                ->with([
                    'sections',
                ])
            ->when($this->section, fn ($query) => $query->whereRelation('sections', 'section_id', $this->section->id))
            ;
    }

    public function getTableHeading(): string|Closure|null
    {
        return $this->section
            ? 'Characters'
            : null;
    }

    public function getTableHeaderActions(): ?array
    {
        return [
            Tables\Actions\Action::make('addCharactersToSectionHeaderAction')
                ->visible((bool) $this->section)
                ->label('Add characters to the section')
                ->url(route('characters.datatable', ['section' => $this->section]))
                ->icon('heroicon-o-plus'),
        ];
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
                ->extraAttributes(['class' => '']),
            Tables\Columns\TextColumn::make('videos_count')
                ->label('Videos')
                ->counts('videos'),
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
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('edit')
                ->url(fn (Character $record): string => route('characters.edit', $record)),
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


    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('addToSection')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-color-swatch')
                ->modalButton('Add to section')
                ->modalHeading('Add characters to section')
                ->modalSubheading('Select section to add characters')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->sections()->syncWithoutDetaching($data['section_id']);
                    }
                    Notification::make()->title('Characters added successfully!')->success()->send();
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
                    Notification::make()->title('Characters removed successfully!')->success()->send();
                }),
        ];
    }


    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No characters found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new characters';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Create character')
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
                @if(! $this->section)
                  <x-header-simple title="Characters" icon="heroicon-o-sparkles">
                          @if($paramSection)
                                <x-href href="{{ route('sections.show', $paramSection) }}" color="gray">
                                      <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                                      Back to Section
                                </x-href>
                           @endif
                        <x-href href="{{ route('characters.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              Create Character
                        </x-href>
                  </x-header-simple>
                  @endif

                 {{ $this->table }}

            </div>
        blade;
    }

//    protected function getTableContentGrid(): ?array
//    {
//        return [
//            'md' => 3,
//            'xl' => 4,
//        ];
//    }
}
