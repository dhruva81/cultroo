<?php

namespace App\Livewire\Users;

use App\Models\PendingUser;
use App\Models\User;
use App\Rules\OTPValidationRule;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterSuperAdmin extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public string $name = '';

    public string $email = '';

    public string $otp = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $superAdminCount = User::where('user_type', 'super_admin')->count();

        abort_if($superAdminCount > 0, 403, 'You are not allowed to access this page.');

        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->placeholder('Enter your name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->placeholder('Enter your email')
                ->email()
                ->unique(User::class)
                ->required(),
            Forms\Components\TextInput::make('password')
                ->password()
                ->minLength(8)
                ->same('password_confirmation')
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->required(),
            Forms\Components\TextInput::make('password_confirmation')
                ->label('Password Confirmation')
                ->password()
                ->dehydrated(false)
                ->required(),
        ];
    }

    public function submit()
    {

        $user = User::create($this->form->getState() + [
                'user_type' => 'super_admin',
                'email_verified_at' => now(),
            ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('auth.register-super-admin')
            ->layout('layouts.guest');
    }
}
