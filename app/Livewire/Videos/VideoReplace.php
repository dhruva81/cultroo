<?php

namespace App\Livewire\Videos;

use App\Enums\TranscodingStatus;
use App\Forms\Components\VideoUpload;
use App\Models\Character;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Series;
use App\Models\Video;
use Filament\Forms;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class VideoReplace extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Video $video = null;
    public $video_file = null;
    public bool $isVideoFileAvailable = false;

    public function updatedVideoFile()
    {
        $this->isVideoFileAvailable = true;
    }

    public function mount(): void
    {

        $this->form->fill(
            $this->video ?
                [
                    'title' => $this->video->title,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\ViewField::make('video_file')
                ->disableLabel()
                ->view('forms.components.video-upload')
                ->required(),
        ];
    }

    protected function getFormModel(): Video|string
    {
        return $this->video ? $this->video : Video::class;
    }

    public function submit()
    {
        $data = $this->form->getState();

        if($data['video_file'] instanceof TemporaryUploadedFile)
        {
            $ext = pathinfo($data['video_file']->getClientOriginalName(), PATHINFO_EXTENSION);
            $uploadedVideoPath = 'video_uploads' . '/' . Str::uuid() .'.'. $ext;

            Storage::disk('s3')->move($data['video_file']->getRealPath(),  $uploadedVideoPath);

            $this->video->file_name = $data['video_file']->getClientOriginalName();
            $this->video->uploaded_video_path = $uploadedVideoPath;
            $this->video->streamable_video_path = null;
            $this->video->streamable_video_meta = null;
            $this->video->status = Video::STATUS_DRAFT;
            $this->video->transcoding_status = TranscodingStatus::PENDING->value;
            $this->video->save();
        }

        session()->flash('success', 'Video file updated successfully');

        return redirect()->route('videos.show', $this->video);
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                @include('app.videos.video-header')

               <x-header-simple title="Update video file" />

                   <div class="px-4 py-2 mb-4 border border-blue-300 rounded-lg bg-blue-50 dark:bg-blue-300">
                      <div class="flex items-top">
                            <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5 mr-2 text-blue-900" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            <h3 class="text-base font-medium text-blue-900">
                                <span class="font-normal">
                                    The existing video file will be replaced with the new one. Old video file and its transcoded files will be deleted.
                                </span>
                            </h3>
                      </div>
                </div>

                  <form wire:submit.prevent="submit">

                        {{ $this->form }}

                        <div class="flex justify-between my-8">
                            <x-button-loader
                                  color="indigo"
                                  type="submit"
                                  wire:target="submit"
                                  >
                                    Update
                            </x-button-loader>
                            <x-href
                                href="{{ route('videos.show', $this->video) }}"
                                color="gray"
                                >
                                    Cancel
                            </x-href>
                        </div>

                  </form>

                  {{ $this->modal }}
            </div>
        blade;
    }
}
