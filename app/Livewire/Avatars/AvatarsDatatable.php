<?php

namespace App\Livewire\Avatars;

use App\Models\Avatar;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class AvatarsDatatable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Avatar::query();
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
            Tables\Columns\ImageColumn::make('avatar_path')
                ->disk('s3'),
            Tables\Columns\TextColumn::make('avatar_category')
                ->sortable()
                ->label('Category'),
            Tables\Columns\TextColumn::make('created_at')
                ->sortable()
                ->date(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('updateCategoryName')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->avatar_category = $data['avatar_category'];
                        $record->save();
                    }
                    Notification::make()
                        ->title('Avatar category name updated successfully')
                        ->success()
                        ->send();
                })
                ->form([
                    Forms\Components\TextInput::make('avatar_category')
                        ->label('Category Name')
                        ->dehydrateStateUsing(fn ($state) => ucwords($state))
                        ->required(),
                ])
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->color('primary')
                ->icon('heroicon-o-pencil-alt')
                ->modalButton('Yes! update category'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('edit')
                ->url(fn (Avatar $record): string => route('avatars.edit', $record)),
        ];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No avatars found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Change your filters or create a new avatar.';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50, 100];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                  <x-header-simple title="Profile Avatars" icon="heroicon-o-emoji-happy">
                        <x-href href="{{ route('avatars.datatable') }}" color="white">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go Back
                        </x-href>
                        <x-href href="{{ route('avatars.create') }}">
                              <x-heroicon-o-plus class="h-5 w-5 -ml-2 mr-2" />
                              Add Avatars
                        </x-href>
                  </x-header-simple>

                 {{ $this->table }}

            </div>
        blade;
    }
}
