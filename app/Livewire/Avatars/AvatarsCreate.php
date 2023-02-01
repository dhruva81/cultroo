<?php

namespace App\Livewire\Avatars;

use App\Models\Avatar;
use Filament\Forms;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class AvatarsCreate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('avatar_category')
                ->dehydrateStateUsing(fn ($state) => ucwords($state))
                ->label('Avatar Group/Category Name'),
            Forms\Components\FileUpload::make('avatar_path')
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    return (string) str($file->getClientOriginalName())->prepend(Str::random(4).'-');
                })
                ->label('Avatar')
                ->image()
                ->columns(2)
                ->required()
                ->disk('s3')
                ->preserveFilenames()
                ->multiple()
                ->directory('avatars')
                ->visibility('public'),
        ];
    }

    public function submit()
    {
        foreach($this->form->getState()['avatar_path'] as $avatar) {
            Avatar::create([
                'avatar_category' => $this->form->getState()['avatar_category'],
                'avatar_path' => $avatar,
            ]);
        }
        session()->flash('success', 'Avatar created successfully!');
        return redirect()->route('avatars.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-xl mx-auto my-8">

                  <x-header-simple title="Add Avatars">
                        <x-href href="{{route('avatars.datatable')  }}">
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
