<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class NavigationLinks
{
    public static function newNavigationGroupsLinks(): array
    {
        return [
            'Content' => [
                [
                    'name' => 'Shorts',
                    'icon' => 'heroicon-o-video-camera',
                    'url' => route('shorts.datatable'),
                    'active' => request()->is('admin/shorts') || request()->is('admin/shorts/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Series',
                    'icon' => 'heroicon-o-collection',
                    'url' => route('series.datatable'),
                    'active' => request()->is('admin/series') || request()->is('admin/series/*'),
                    'isVisible' => true,
                ],
            ],
            'Filters' => [
                [
                    'name' => 'Collection',
                    'icon' => 'heroicon-o-color-swatch',
                    'url' => route('collections.datatable'),
                    'active' => request()->is('admin/collection') || request()->is('admin/collection/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Categories',
                    'icon' => 'heroicon-o-adjustments',
                    'url' => route('genres.datatable'),
                    'active' => request()->is('admin/categories') || request()->is('admin/categories/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Characters',
                    'icon' => 'heroicon-o-sparkles',
                    'url' => route('characters.datatable'),
                    'active' => request()->is('admin/characters') || request()->is('admin/characters/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Languages',
                    'icon' => 'heroicon-o-translate',
                    'url' => route('languages.datatable'),
                    'active' => request()->is('admin/languages') || request()->is('admin/languages/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Tags',
                    'icon' => 'heroicon-o-tag',
                    'url' => route('tags.datatable'),
                    'active' => request()->is('admin/tags') || request()->is('admin/tags/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Profile Avatars',
                    'icon' => 'heroicon-o-emoji-happy',
                    'url' => route('avatars.datatable'),
                    'active' => request()->is('admin/avatars') || request()->is('admin/avatars/*'),
                    'isVisible' => true,
                ],
            ],
            'App' => [
                [
                    'name' => 'Sections',
                    'icon' => 'heroicon-o-server',
                    'url' => route('sections.datatable'),
                    'active' => Route::currentRouteName() === 'sections.datatable',
                    'isVisible' => true,
                ],
            ],
            'More' => [
                [
                    'name' => 'Admin Links',
                    'icon' => 'heroicon-o-plus-circle',
                    'url' => route('more-links'),
                    'active' => Route::currentRouteName() === 'more-links',
                    'isVisible' => true,
                ],
            ],
            'Logs' => [
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
            ],
            'Business' => [
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
            ],

            'Help and Support' => [
                [
                    'name' => 'Support Categories',
                    'icon' => 'heroicon-o-support',
                    'url' => route('support-categories.datatable'),
                    'active' => request()->is('admin/support-categories') or request()->is('admin/support-categories/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Support FAQs',
                    'icon' => 'heroicon-o-question-mark-circle',
                    'url' => route('support-faqs.datatable'),
                    'active' => request()->is('admin/support-faqs') or request()->is('admin/support-faqs/*'),
                    'isVisible' => true,
                ],
            ],

            'Manage' => [
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
            ],
            'Settings' => [
                [
                    'name' => 'AWS Sync',
                    'url' => route('videos.sync'),
                    'icon' => 'heroicon-o-cloud',
                    'active' => request()->is('sync'),
                    'isVisible' => false,
                ],
                [
                    'name' => 'Your Account',
                    'url' => '/user/profile',
                    'icon' => 'heroicon-o-user-circle',
                    'active' => request()->is('user/profile') or request()->is('user/profile/*'),
                    'isVisible' => true,
                ],
                [
                    'name' => 'Notifications',
                    'url' => '#',
                    'icon' => 'heroicon-o-bell',
                    'isVisible' => true,
                ],
            ],
            'Dev Tools' => [
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
            ],

        ];
    }

    public static function getSidebarColorTheme(): array
    {
        $color = 'default';

        return [
            'default' => [
                'bgColor' => ' bg-blue-900 ',
                'borderColor' => ' border-gray-900 ',
                'dividerColor' => ' divide-gray-900 ',
                'labelColor' => ' text-gray-100  ',
                'defaultState' => ' text-gray-100 hover:bg-white hover:text-gray-900',
                'activeState' => ' text-gray-900 bg-white ',
            ],
            'cyan' => [
                'bgColor' => ' bg-cyan-700 ',
                'dividerColor' => ' divide-cyan-800 ',
                'borderColor' => ' border-cyan-800 ',
                'labelColor' => ' text-gray-100  ',
                'defaultState' => ' text-cyan-100 hover:text-white hover:bg-cyan-600',
                'activeState' => ' bg-cyan-800 text-white ',
            ],
            'gray' => [
                'bgColor' => ' bg-gray-100 ',
                'dividerColor' => ' divide-gray-200 ',
                'borderColor' => ' border-gray-200 ',
                'labelColor' => ' text-gray-800  ',
                'defaultState' => ' text-gray-600 hover:text-gray-900 hover:bg-gray-300 ',
                'activeState' => ' bg-gray-300 text-gray-900 ',
            ],

        ][$color];
    }

    public static function getQuickCreateLinks(): array
    {
        return [
            [
                'name' => 'Series',
                'icon' => 'heroicon-o-collection',
                'url' => route('series.create'),
                'isVisible' => true,
            ],
            [
                'name' => 'Collections',
                'icon' => 'heroicon-o-color-swatch',
                'url' => route('collections.create'),
                'isVisible' => true,
            ],
            [
                'name' => 'Genre',
                'icon' => 'heroicon-o-adjustments',
                'url' => route('genres.create'),
                'isVisible' => true,
            ],
            [
                'name' => 'Character',
                'icon' => 'heroicon-o-sparkles',
                'url' => route('characters.create'),
                'isVisible' => true,
            ],
        ];
    }
}
