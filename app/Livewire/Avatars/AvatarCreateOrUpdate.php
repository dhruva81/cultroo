<?php

namespace App\Livewire\Avatars;

use App\Models\Avatar;
use Filament\Forms;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class AvatarCreateOrUpdate extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Avatar $avatar = null;

    public ?string $name = null;

    public ?string $status = null;

    public function mount(): void
    {
        $this->form->fill(
            $this->avatar ?
                [
                    'avatar_category' => $this->avatar->avatar_category,
                    'avatar_path' => $this->avatar->avatar_path,
                ] : []
        );
    }

    protected function getFormSchema(): array
    {
        return [

            Forms\Components\FileUpload::make('avatar_path')
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    return (string) str($file->getClientOriginalName())->prepend(Str::random(4).'-');
                })
                ->label('Avatar')
                ->required()
                ->disk('s3')
                ->preserveFilenames()
                ->directory('avatars')
                ->visibility('public'),
            Forms\Components\TextInput::make('avatar_category')
                ->dehydrateStateUsing(fn ($state) => ucwords($state))
                ->label('Avatar Group/Category Name')
                ->required()
        ];
    }

    protected function getFormModel(): Avatar|string
    {
        return $this->avatar ? $this->avatar : Avatar::class;
    }

    public function submit()
    {
        if ($this->avatar) {
            $this->avatar->update($this->form->getState());
            session()->flash('success', 'Avatar updated successfully!');
            return redirect()->route('avatars.datatable');
        }

        $avatar = Avatar::create($this->form->getState());
        session()->flash('success', 'Avatar created successfully!');
        return redirect()->route('avatars.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-xl mx-auto my-8">

                  <x-header-simple title="{{ $this->avatar ? 'Edit Avatar' : 'Create Avatar' }}">
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
