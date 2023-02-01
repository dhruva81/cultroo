<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class UserShow extends Component
{
    public User $user;

    public function render()
    {
        return view('app.users.user-show');
    }
}
