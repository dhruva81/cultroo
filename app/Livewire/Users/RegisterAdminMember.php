<?php

namespace App\Livewire\Users;

use App\Models\PendingUser;
use App\Models\User;
use App\Rules\OTPValidationRule;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterAdminMember extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public string $name = '';

    public string $email = '';

    public string $otp = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('email')
                ->placeholder('Enter your email')
                ->email()
                ->reactive()
                ->unique(User::class)
                ->exists(table: PendingUser::class)
                ->required(),
            Forms\Components\TextInput::make('otp')
                ->label('OTP')
                ->numeric()
                ->minLength(4)
                ->rules([fn (\Closure $get, $state) => new OTPValidationRule($get('email'), $state)])
                ->placeholder('Enter your otp')
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
        $pendingUser = PendingUser::where([
            'email' => $this->form->getState()['email'],
        ])->first();

        $pendingUserName = $pendingUser && $pendingUser?->name
                        ? $pendingUser->name
                        : 'Admin user';

        $user = User::create($this->form->getState() + [
            'user_type' => 'admin',
            'name' => $pendingUserName,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('auth.register-admin-members')
            ->layout('layouts.guest');
    }
}
