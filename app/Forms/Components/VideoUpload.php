<?php

namespace App\Forms\Components;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Field;
use Livewire\WithFileUploads;

class VideoUpload extends BaseFileUpload
{
    protected string $view = 'forms.components.video-upload';

    public function yo(): bool
    {
        return $this->getLivewire()->isVideoFileAvailable;
    }

    public function resetForm()
    {
        $this->getLivewire();
        ray('resetting form');
    }


}
