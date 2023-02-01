<?php

namespace App\Livewire\Sections;

use App\Models\Section;
use Filament\Forms;
use Filament\Tables\Actions\Action;
use Livewire\Component;

class SectionCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Section $section = null;

    public ?string $name = null;

    public ?string $status = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->section ?
                [
                    'name' => $this->section->name,
                    'color' => $this->section->color,
                    'status' => $this->section->status,
                    'model' => $this->section->model,
                    'layout' => $this->section->layout,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Section Title')
                ->dehydrateStateUsing(fn($state) => ucwords($state))
                ->required(),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Select::make('status')
                        ->required()
                        ->placeholder('Select Status')
                        ->label('Status')
                        ->default(Section::STATUS_DRAFT)
                        ->options([
                            Section::STATUS_DRAFT => 'Draft',
                            Section::STATUS_REVIEW => 'Review',
                            Section::STATUS_PUBLISHED => 'Published',
                        ]),
                    Forms\Components\ColorPicker::make('color')
                        ->label('Background Color/Section theme'),
                    Forms\Components\Select::make('model')
                        ->hidden((bool) $this->section)
                        ->required()
                        ->placeholder('Select Model')
                        ->label('Model')
                        ->default(Section::STATUS_DRAFT)
                        ->options([
                            'series' => 'Series',
                            'video' => 'Shorts',
//                            'kollection' => 'Collections',
                            'character' => 'Characters',
//                            'genre' => 'Categories',
                            'language' => 'Language',
                        ]),
                    Forms\Components\Select::make('layout')
                        ->required()
                        ->placeholder('Select Layout')
                        ->label('Layout')
                        ->default(1)
                        ->options([
                                1   => 'Layout 1',
                                2   => 'Layout 2',
                                3   => 'Layout 3',
                        ]),
                ]),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('icon')
                        ->image()
                        ->label('Icon')
                        ->visibility('public')
                        ->collection('icon')
                        ->preserveFilenames()
                        ->disk('s3'),
                    Forms\Components\SpatieMediaLibraryFileUpload::make('cover_image')
                        ->image()
                        ->label('Cover Image')
                        ->visibility('public')
                        ->collection('cover_image')
                        ->preserveFilenames()
                        ->disk('s3'),
                ]),
        ];
    }

    protected function getFormModel(): Section|string
    {
        return $this->section ? $this->section : Section::class;
    }

    public function submit()
    {
        if ($this->section) {
            $this->section->update($this->form->getState());
            $this->form->model($this->section)->saveRelationships();

            session()->flash('success', 'Section updated successfully!');

            return redirect()->route('sections.show', $this->section);
        }

        $section = Section::create($this->form->getState() + ['status' => 'draft']);
        $this->form->model($section)->saveRelationships();

        session()->flash('success', 'Section created successfully!');

        return redirect()->route('sections.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="{{ $this->section ? 'Edit Section' : 'Create Section' }}">
                        <x-href href="{{route('sections.datatable')  }}">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go Back
                        </x-href>
                  </x-header-simple>

                  <form wire:submit.prevent="submit">

                        {{ $this->form }}

                        <x-button-loader
                              class="my-1 mt-8"
                              color="indigo"
                              size="xl"
                              type="submit"
                              wire:target="submit"
                              >
                             Submit
                        </x-button-loader>
                  </form>
            </div>
        blade;
    }
}
