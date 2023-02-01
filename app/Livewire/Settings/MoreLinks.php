<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class MoreLinks extends Component
{
    public array $links = [];

    public function mount()
    {
        $this->links = [
            [
                'name' => 'Watch History',
                'icon' => 'heroicon-o-clock',
                'url' => route('watch-history.datatable'),
                'active' => Route::currentRouteName() === 'watch-history.datatable',
                'isVisible' => false,
            ],
            [
                'name' => 'Activity Logs',
                'url' => route('activity-logs.datatable'),
                'icon' => 'heroicon-o-cursor-click',
                'active' => Route::currentRouteName() === 'activity-logs.datatable',
                'isVisible' => false,
            ],
            [
                'name' => 'Tokens Logs',
                'url' => route('access-tokens.datatable'),
                'icon' => 'heroicon-o-finger-print',
                'active' => Route::currentRouteName() === 'access-tokens.datatable',
                'isVisible' => false,
            ],
            [
                'name' => 'Auth Logs',
                'url' => route('auth-logs.datatable'),
                'active' => Route::currentRouteName() === 'auth-logs.datatable',
                'icon' => 'heroicon-o-login',
                'isVisible' => false,
            ],
            [
                'name' => 'Email Logs',
                'url' => route('email-logs.datatable'),
                'active' => Route::currentRouteName() === 'email-logs.datatable',
                'icon' => 'heroicon-o-mail',
                'isVisible' => false,
            ],
            [
                'name' => 'AWS Sync',
                'url' => route('videos.sync'),
                'active' => request()->is('sync'),
                'icon' => 'heroicon-o-cloud',
                'isVisible' => false,
            ],
            [
                'name' => 'Media Convert Responses',
                'url' => route('media-convert-responses.datatable'),
                'active' => Route::currentRouteName() === 'sns-responses.datatable',
                'icon' => 'heroicon-o-cloud',
                'isVisible' => false,
            ],
            [
                'name' => 'Untranscoded Videos',
                'url' => route('videos.untranscoded.datatable'),
                'active' => Route::currentRouteName() === 'videos.untranscoded.datatable',
                'icon' => 'heroicon-o-video-camera',
                'isVisible' => false,
            ],
            [
                'name' => 'Plans',
                'icon' => 'heroicon-o-color-swatch',
                'url' => route('plans.datatable'),
                'active' => request()->is('admin/plans') or request()->is('admin/plans/*'),
                'isVisible' => false,
            ],
            [
                'name' => 'Subscriptions',
                'icon' => 'heroicon-o-ticket',
                'url' => route('subscriptions.datatable'),
                'active' => request()->is('admin/subscriptions') or request()->is('admin/subscriptions/*'),
                'isVisible' => false,
            ],
            [
                'name' => 'Transactions',
                'icon' => 'heroicon-o-currency-dollar',
                'url' => '#',
                'active' => request()->is('admin/transactions') || request()->is('admin/transactions/*'),
                'isVisible' => false,
            ],
            [
                'name' => 'Customer Users',
                'icon' => 'heroicon-o-user-group',
                'url' => route('users.datatable'),
                'active' => request()->is('admin/users') or request()->is('admin/users/*'),
                'isVisible' => false,
            ],
            [
                'name' => 'Admin Members',
                'url' => route('members.datatable'),
                'active' => request()->is('admin/members') or request()->is('admin/members/*'),
                'icon' => 'heroicon-o-users',
                'isVisible' => false,
            ],
            [
                'name' => 'Roles & Privileges',
                'url' => '#',
                'active' => request()->is('admin/roles') or request()->is('admin/roles/*'),
                'icon' => 'heroicon-o-key',
                'isVisible' => false,
            ],
            [
                'name' => 'Api Docs',
                'url' => '/docs',
                'icon' => 'heroicon-o-external-link',
                'isVisible' => false,
                'openInNewTab' => true,
            ],
            [
                'name' => 'Log Viewer',
                'url' => '/logs',
                'icon' => 'heroicon-o-external-link',
                'isVisible' => false,
                'openInNewTab' => true,
            ],

            [
                'name' => 'Telescope',
                'url' => '/telescope',
                'icon' => 'heroicon-o-external-link',
                'isVisible' => false,
                'openInNewTab' => true,
            ],
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div class="max-w-3xl mx-auto">

                    <div class="pt-8 pb-8 border-b border-gray-200">
                        <h3 class="pl-4 text-xl leading-6 font-semibold text-gray-900">
                            Quick Links
                        </h3>
                    </div>


                    <div class="grid grid-cols-1 gap-x-4 sm:grid-cols-1">

                        @foreach($links as $link)
                            <div class="border-b border-gray-200 hover:bg-white pl-4">
                                <a href="{{ $link['url'] }}" class="focus:outline-none">
                                    <div class="relative group py-4 flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-lg {{ isset($link['iconColor']) ? $link['iconColor'] : 'bg-gray-200 text-blue-900' }}">
                                                @svg($link['icon'], "h-6 w-6")
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                {{ $link['name'] }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 self-center">
                                            <!-- Heroicon name: solid/chevron-right -->
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>

            </div>

        blade;
    }
}
