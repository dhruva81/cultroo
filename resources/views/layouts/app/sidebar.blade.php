@php

    $theme = \App\Services\NavigationLinks::getSidebarColorTheme();
    $bgColor = $theme['bgColor'];

@endphp

<div
    class="hidden md:flex md:flex-col md:fixed md:inset-y-0 {{ $bgColor }}"
    :class="{ 'md:w-16 ' : sidebarMini, 'md:w-64': !sidebarMini, }"
    x-transition:enter="transition ease-out duration-200 transform"
    x-transition:enter-start="-translate-x-64"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-out duration-200 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-64"

    x-cloak
>
    @include('layouts.app.navigation')
</div>

<!-- Sidebar Mobile -->
<div x-cloak x-show="sidebarMobile" class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true" >
    <div class="fixed inset-0 bg-gray-300 bg-opacity-75" aria-hidden="true"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full {{ $bgColor }}">
        <div x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-description="Close button, show/hide based on off-canvas menu state." class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="sidebarMobile = !sidebarMobile" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <span class="sr-only">Close sidebar</span>
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @include('layouts.app.navigation')
    </div>
</div>
