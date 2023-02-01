<?php

namespace App\Livewire\Videos;

use App\Forms\Components\VideoUpload;
use App\Jobs\VideoTransferInAWS;
use App\Models\Character;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Series;
use App\Models\Video;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class VideoCreateFromAws extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Video $video = null;
    public ?Series $series = null;

    public string $pageTitle = 'Create Video';
    public ?string $goBackRoute = null;
    public ?string $title = null;
    public ?string $short_description = null;
    public string $status = 'draft';
    public ?string $description = null;
    public $video_file = null;
    public $isVideoFileAvailable = false;
    public ?string $file = null;

    public function updatedVideoFile()
    {
        $this->isVideoFileAvailable = true;
    }

    public function mount(): void
    {
        $this->file = session()->has('aws-video') ? session()->get('aws-video') : null;
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('Video Title')
                ->required(),
            Forms\Components\Toggle::make('is_series_episode')
                ->label('Is this video a series episode?')
                ->reactive()
                ->inline(),
            Forms\Components\Select::make('series_id')
                ->visible(fn() => $this->is_series_episode)
                ->label('Series')
                ->options(Series::pluck('title', 'id'))
                ->placeholder('Select a series')
                ->searchable(),
            Forms\Components\Select::make('genres')
                ->visible(fn() => !$this->is_series_episode)
                ->multiple()
                ->relationship('genres', 'name')
                ->options(Genre::pluck('name', 'id'))
                ->placeholder('Select Genres')
                ->searchable(),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Select::make('language_id')
                        ->options(Language::pluck('name', 'id'))
                        ->label('Language')
                        ->required(),
                    Forms\Components\TextInput::make('min_age')
                        ->numeric()
                        ->minValue(1)
                        ->default(1),
                ]),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\SpatieTagsInput::make('tags'),
                    Forms\Components\Select::make('characters')
                        ->multiple()
                        ->relationship('characters', 'name')
                        ->options(Character::pluck('name', 'id'))
                        ->placeholder('Select an character')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->disableLabel()
                                ->placeholder('Enter a character name')
                                ->unique('characters', 'name')
                                ->required(),
                        ])
                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                            return $action
                                ->modalHeading('Create character')
                                ->modalButton('Create')
                                ->modalWidth('lg');
                        })
                        ->searchable(),
                ]),
            Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                ->visibility('public')
                ->collection('thumbnail')
                ->preserveFilenames()
                ->disk('s3'),
            Forms\Components\Textarea::make('description'),

        ];
    }

    protected function getFormModel(): Video|string
    {
        return $this->video ? $this->video : Video::class;
    }

    public function submit()
    {
        // Create Video Record
        $video = Video::create($this->form->getState());
        $this->form->model($video)->saveRelationships();

        // Move video from aws dump folder to permanent location
        $ext = pathinfo($this->file, PATHINFO_EXTENSION);
        $data = [
            'video_id' => $video->id,
            'file_name' => basename($this->file),
            'temp_path' => $this->file,
            'uploaded_video_path' => 'video_uploads' . '/' . Str::uuid() .'.'. $ext
        ];

        VideoTransferInAWS::dispatch($data);
        
        return  redirect()->route('videos.show', $video);
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

               <x-header-simple title="Create video record" />

                @if($this->file)
                <div class="px-4 py-2 mb-4 border border-blue-300 rounded-lg bg-blue-50 dark:bg-blue-300">
                      <div class="flex items-center">
                            <svg aria-hidden="true" class="w-5 h-5 mr-2 text-blue-900" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            <h3 class="text-base font-medium text-blue-900">
                                <span class="font-semibold">{{ $this->file }}</span>
                            </h3>
                      </div>
                </div>
                @endif

                  <form wire:submit.prevent="submit">

                        {{ $this->form }}

                        <div class="flex space-x-4 mt-8">
                            <x-button-loader
                                  color="indigo"
                                  type="submit"
                                  wire:target="submit"
                                  >
                                 Create
                            </x-button-loader>

                            <div wire:loading.remove>
                                <x-href
                                      href="{{ route('videos.sync') }}"
                                      color="gray"
                                      >
                                     Cancel
                                </x-href>
                            </div>

                        </div>

                  </form>

                  {{ $this->modal }}
            </div>
        blade;
    }
}
