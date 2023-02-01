<?php

namespace App\Livewire\Videos;

use App\Forms\Components\VideoUpload;
use App\Jobs\VideoTransferInAWS;
use App\Models\Character;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Series;
use App\Models\Tag;
use App\Models\Video;
use App\Rules\TagValidationRule;
use Filament\Forms;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class VideoCreateOrUpdate extends Component implements Forms\Contracts\HasForms
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
    public ?string $routeName = null;

    public function updatedVideoFile()
    {
        $this->isVideoFileAvailable = true;
    }

    public function mount(): void
    {
        $this->routeName = Route::currentRouteName();
        if($this->routeName === 'shorts.create')
        {
            $this->pageTitle = 'Create Short';
            $this->goBackRoute = route('shorts.datatable');
        }elseif($this->routeName === 'episodes.create'){
            $this->pageTitle = 'Create Episode';
            $this->goBackRoute = route('series.show', $this->series);
        }else{
            $this->pageTitle = 'Edit';
            $this->goBackRoute = route('videos.show', $this->video);
        }

        $this->form->fill(
            $this->video ?
                [
                    'title' => $this->video->title,
                    'description' => $this->video->description,
                    'min_age' => $this->video->min_age,
                    'max_age' => $this->video->max_age,
                    'language_id' => $this->video->language_id,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make([
                'default' => 1,
                'sm' => 3,
            ])
                ->schema([
                    Forms\Components\Group::make()
                        ->columnSpan(2)
                        ->schema([
                            Forms\Components\Card::make()
                                ->hidden((bool) $this->video)
                                ->schema([
                                    Forms\Components\ViewField::make('video_file')
                                        ->disableLabel()
                                        ->view('forms.components.video-upload')
                                        ->required(),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->maxLength(65)
                                        ->label('Video Title'),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Textarea::make('description')
                                        ->minLength(60)
                                        ->maxLength(308),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('english_subtitle')
                                        ->visibility('public')
                                        ->collection('english_subtitle')
                                        ->preserveFilenames()
                                        ->disk('s3'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('hindi_subtitle')
                                        ->visibility('public')
                                        ->collection('hindi_subtitle')
                                        ->preserveFilenames()
                                        ->disk('s3'),
                                ]),
                        ]),
                    Forms\Components\Group::make()
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                                        ->visibility('public')
                                        ->collection('thumbnail')
                                        ->preserveFilenames()
                                        ->disk('s3'),
                                ]),
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('language_id')
                                        ->options(Language::pluck('name', 'id'))
                                        ->label('Language'),
                                ]),
                            Forms\Components\Card::make()
                                ->visible( $this->routeName == 'shorts.create' || ($this->series == null  && $this->video?->series_id == null))
                                ->schema([
                                    Forms\Components\Select::make('genres')
                                        ->multiple()
                                        ->label('Category')
                                        ->relationship('genres', 'name')
                                        ->options(Genre::pluck('name', 'id'))
                                        ->placeholder('Select Category')
                                        ->searchable(),
                                ]),
                            Forms\Components\Card::make()
                                ->visible( $this->routeName == 'shorts.create' || ($this->series == null  && $this->video?->series_id == null))
                                ->schema([
                                    Forms\Components\Select::make('characters')
                                        ->multiple()
                                        ->relationship('characters', 'name')
                                        ->options(Character::pluck('name', 'id'))
                                        ->placeholder('Select a character')
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
                            Forms\Components\Card::make()
                                ->visible( $this->routeName == 'shorts.create' || ($this->series == null  && $this->video?->series_id == null))
                                ->schema([
                                    Forms\Components\Select::make('tags')
                                        ->multiple()
                                        ->relationship('tags', 'name')
                                        ->options(Tag::pluck('name', 'id'))
                                        ->placeholder('Select a tag')
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->disableLabel()
                                                ->disableAutocomplete()
                                                ->placeholder('Enter a tag')
                                                ->required()
                                                ->rules([fn ($state) => new TagValidationRule($state)])
                                                ->dehydrateStateUsing(fn ($state) => str_replace('#', '', strtolower($state)))
                                        ])
                                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                            return $action
                                                ->modalHeading('Create tag')
                                                ->modalButton('Create')
                                                ->modalWidth('lg');
                                        })
                                        ->searchable(),
                                ]),
                            Forms\Components\Card::make()
                                ->visible( $this->routeName == 'shorts.create' || ($this->series == null  && $this->video?->series_id == null))
                                ->schema([
                                    Forms\Components\Grid::make()
                                        ->schema([
                                            Forms\Components\TextInput::make('min_age')
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(1),
                                            Forms\Components\TextInput::make('max_age')
                                                ->numeric()
                                                ->gte('min_age'),
                                        ])
                                ]),
                        ]),
                ]),
        ];
    }

    protected function getFormModel(): Video|string
    {
        return $this->video ? $this->video : Video::class;
    }

    public function submit()
    {
        ray($this->form->getState());
        // Update video details
        if ($this->video) {
            $this->video->update($this->form->getState());
            $this->form->model($this->video)->saveRelationships();
            session()->flash('success', 'Video updated successfully!');
            return redirect()->route('videos.show', ['video' => $this->video]);
        }

        // Create new video
        $data = $this->form->getState();

        if($data['title'] == null){
            $data['title'] = $data['video_file'] instanceof TemporaryUploadedFile ? $data['video_file']->getClientOriginalName() : 'Untitled';
        }

        $video = Video::create($data + [
            'series_id' => $this->series?->id,
            ]);


        // Move video from temporary folder to permanent location
        if($data['video_file'] instanceof TemporaryUploadedFile)
        {
            $ext = pathinfo($data['video_file']->getClientOriginalName(), PATHINFO_EXTENSION);
            $data = [
                'video_id' => $video->id,
                'file_name' => $data['video_file']->getClientOriginalName(),
                'temp_path' => $data['video_file']->getRealPath(),
                'uploaded_video_path' => 'video_uploads' . '/' . Str::uuid() .'.'. $ext
            ];
            VideoTransferInAWS::dispatch($data);
        }

        $this->form->model($video)->saveRelationships();
        session()->flash('success', 'Video created successfully!');

        return $this->series
                ? redirect()->route('series.show', $this->series)
                : redirect()->route('shorts.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-7xl mx-auto my-8">

               <x-header-simple :title="$this->pageTitle">
                      @if($this->goBackRoute)
                        <x-href :href="$this->goBackRoute" color="gray" size="sm">
                             <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                             Back
                        </x-href>
                    @endif
               </x-header-simple>

                @if($this->series)
                <div class="px-4 py-2 mb-4 border border-blue-300 rounded-lg bg-blue-50 dark:bg-blue-300">
                      <div class="flex items-center">
                            <svg aria-hidden="true" class="w-5 h-5 mr-2 text-blue-900" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            <h3 class="text-base font-medium text-blue-900">
                                <span class="font-semibold">{{ $this->series?->title }}</span>.
                            </h3>
                      </div>
                      <div class="text-xs ml-6 pl-1">
                            You are adding episode in this series.
                      </div>
                </div>
                @endif


                  <form wire:submit.prevent="submit">

                        {{ $this->form }}

                        <x-button-loader
                              class="my-1 mt-8"
                              color="indigo"
                              size="xl"
                              type="submit"
                              wire:target="submit"
                              >
                             {{ $this->video ? 'Update' : 'Create'}}
                        </x-button-loader>

                  </form>

                  {{ $this->modal }}
            </div>
        blade;
    }
}
