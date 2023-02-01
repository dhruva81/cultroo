<?php

namespace App\Livewire\Users;

use App\Models\PendingUser;
use App\Notifications\SendOTPBeforeRegistrationNotification;
use Filament\Forms;
use Livewire\Component;

class InviteAdminMembers extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?string $title = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Repeater::make('members')
                ->disableLabel()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->placeholder('Enter name here')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->placeholder('Enter email here')
                        ->email()
                        ->unique('users', 'email')
                        ->required(),
                ])
                ->createItemButtonLabel('Add another email')
                ->disableItemMovement()
                ->columns(2),
        ];
    }

    public function submit()
    {
        $members = $this->form->getState()['members'];

        foreach ($members as $member) {
            PendingUser::firstOrCreate([
                'email' => $member['email'],
                'user_type' => 'admin',
            ], ['name' => $member['name']])
                ->notify(new SendOTPBeforeRegistrationNotification);
        }

        session()->flash('success', 'Invitations sent successfully!');

        return redirect()->route('members.pending.datatable');
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto my-8">

                  <x-header-simple title="Invite Members">
                        <x-href href="{{ route('members.datatable') }}">
                              <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                              Go Back
                        </x-href>
                  </x-header-simple>

                  <form wire:submit.prevent="submit">

                        {{ $this->form }}

                        <x-button-loader
                              class="my-1 w-full mt-8"
                              icon="heroicon-o-mail"
                              color="indigo"
                              size="xl"
                              type="submit"
                              wire:target="submit"
                              >
                             Send Registration Code
                        </x-button-loader>
                  </form>
            </div>
        blade;
    }
}
