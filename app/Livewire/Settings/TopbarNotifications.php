<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class TopbarNotifications extends Component
{
    public int $unreadNotificationsCount = 0;

    public function mount()
    {
        $this->unreadNotificationsCount = 10;
    }

    public function render()
    {
        return <<<'blade'
            <div>
                 <button
                    x-data="{}"
                    x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
                    type="button" class="flex-shrink-0 bg-white p-1 text-gray-400 rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <x-heroicon-o-bell class="h-6 w-6" />
                </button>
            </div>
        blade;
    }
}
