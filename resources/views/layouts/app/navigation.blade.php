@php

    $theme = \App\Services\NavigationLinks::getSidebarColorTheme();
    $labelColor = $theme['labelColor'];
    $dividerColor = $theme['dividerColor'];
    $borderColor = $theme['borderColor'];
    $defaultState = $theme['defaultState'];
    $activeState = $theme['activeState'];
@endphp



<div
    class="flex-1 flex flex-col min-h-0 border-r border-gray-200 transition-all duration-300"
     x-transition:enter="transition ease-out duration-200 transform"
     x-transition:enter-start="-translate-x-64"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-out duration-200 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-64"
>

    <div class="flex items-center flex-shrink-0 px-3 h-16 border-b {{ $borderColor }}">
        <a x-show="!sidebarMini" href="{{ route('dashboard') }}">
            <img class="px-2 transition-all duration-300 h-10 w-auto" src="{{  asset('assets/images/logo-white.svg') }}">
        </a>
    </div>

    <div class="flex-1 flex flex-col py-4 overflow-y-auto scrollbar-hide">
        <nav class="flex-1 space-y-1">
            <div
                x-data="{ tooltip: {} }"
                x-init=" Alpine.effect(() => {
                            if (!sidebarMini) {
                                tooltip = false
                            } else {
                                tooltip = {
                                    content: 'Dashboard',
                                    placement: 'right',
                                }
                            }
                        })"
                x-tooltip.html="tooltip"
            >
                <a
                    href="{{ route('dashboard') }}"
                    class="{{ request()->is('admin/dashboard') ? $activeState : $defaultState }} flex justify-center items-center mx-2 py-2 px-2 rounded-lg  group flex items-center text-sm font-medium transition-all duration-100">

                    <x-dynamic-component component="heroicon-o-home" class="h-5 w-5 shrink-0" />

                    <span x-show="!sidebarMini" class="flex-1 ml-2 whitespace-nowrap font-normal">
                        Dashboard
                    </span>

                </a>
            </div>


            <div class="space-y-4 divide-y {{ $dividerColor  }}">

                @foreach(\App\Services\NavigationLinks::newNavigationGroupsLinks() as $label => $navs)

                    @if(collect($navs)->pluck('isVisible')->contains('true'))
                    <div

                        x-data="{ open: true}" class="pt-6">

                        <button
                            @click="open = !open"
                            x-show="!sidebarMini"
                            class="px-4 flex items-center justify-between w-full"
                        >
                            <p class="font-bold uppercase text-xs tracking-wider {{ $labelColor }}">
                                {{ $label ?? '-' }}
                            </p>

                            <x-heroicon-o-chevron-down class="w-3 h-3 text-gray-600" x-show="open" x-cloak />
                            <x-heroicon-o-chevron-up class="w-3 h-3 text-gray-600" x-show="! open" />

                        </button>


                        <div x-cloak x-show="open"  class="mt-2" role="group">
                            @foreach($navs as $nav)
                                @if($nav['isVisible'])
                                    <div
                                        x-data="{ tooltip: {} }"
                                        x-init=" Alpine.effect(() => {
                                                if (!sidebarMini) {
                                                    tooltip = false
                                                } else {
                                                    tooltip = {
                                                        content: '{{ $nav['name'] }}',
                                                        placement: 'right',
                                                    }
                                                }
                                            })"
                                        x-tooltip.html="tooltip"
                                    >
                                        <a
                                            href="{{ $nav['url'] }}"

                                            @if(isset($nav['openInNewTab']) && $nav['openInNewTab'])
                                                target="_blank"
                                            @endif

                                            class="{{ isset($nav['active']) && $nav['active'] ?  $activeState : $defaultState }} flex justify-center items-center mx-2 my-1 py-2 px-2 rounded-lg group flex items-center text-sm font-medium transition-all duration-100">
                                            @if($nav['icon'])
                                                <x-dynamic-component :component="$nav['icon']" class="h-5 w-5 shrink-0" />
                                            @endif

                                            @if($nav['name'])
                                                <span x-show="!sidebarMini" class="flex-1 ml-2 whitespace-nowrap font-normal">
                                                    {{ $nav['name'] }}
                                                </span>
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

        </nav>
    </div>

{{--    <div class="flex-shrink-0 flex p-4 border-t {{ $borderColor  }}">--}}
{{--        <a href="{{ route('profile.show') }}" class="flex-shrink-0 w-full group block">--}}
{{--            <div class="flex items-center">--}}
{{--                <div>--}}
{{--                    <img class="inline-block h-9 w-9 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">--}}
{{--                </div>--}}
{{--                <div class="ml-3" class="transition-all duration-300 scrollbar-hide" x-show="!sidebarMini">--}}
{{--                    <p class="text-sm font-medium text-gray-100 group-hover:text-gray-200">--}}
{{--                        {{ auth()->user()->name }}--}}
{{--                    </p>--}}
{{--                    <p class="text-xs font-medium text-gray-100 group-hover:text-gray-200">--}}
{{--                        View profile--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </a>--}}
{{--    </div>--}}

</div>
