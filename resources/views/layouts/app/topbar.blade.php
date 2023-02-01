<nav class="bg-white h-16 border-b border-gray-200">
    <div class="px-2 sm:px-4">
        <div class="flex justify-between h-16">

            <div class="flex-1 flex px-2 lg:px-0 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <button :class="{ ' ' : sidebarMini, ' ': !sidebarMini }" class="hidden md:block mx-2 h-8 hover:bg-gray-200 rounded-full " @click="sidebarMini = !sidebarMini">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <div class="hidden md:block ml-6 flex-1">
                    <livewire:settings.global-search />
                </div>
            </div>

            <div class="ml-4 flex items-center space-x-4">

                <div class="relative" x-data="{ openQuickCreate: false }" @keydown.escape="openQuickCreate = false"
                     @click.away="openQuickCreate = false">
                    <button type="button"
                            @click="openQuickCreate = true"
                            class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-blue-800 hover:bg-blue-900 focus:outline-none">
                        <x-heroicon-s-plus-sm class="h-5 w-5"/>
                    </button>
                    <div x-show="openQuickCreate"
                         @dropdown_animation
                         style="display: none;"
                         class="origin-top-right absolute z-10 right-0 mt-2 w-40 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                         role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">

                        @foreach(\App\Services\NavigationLinks::getQuickCreateLinks() as $link)
                            @if($link['isVisible'])
                                <a href="{{ $link['url'] }}"
                                   class="hover:bg-blue-800 hover:text-white group flex items-center px-3 py-2 text-sm font-regular text-gray-800 transition ease-in-out duration-150"
                                   role="menuitem" tabindex="-1" id="user-menu-item-0">
                                    <x-dynamic-component :component="$link['icon']" class="h-5 w-5 mr-3 shrink-0"/>
                                    {{ $link['name'] }}
                                </a>
                            @endif
                        @endforeach

                    </div>
                </div>

                <livewire:settings.topbar-notifications />

                <button class="md:hidden mx-2 h-8 hover:bg-gray-200 rounded-full " @click="sidebarMobile = !sidebarMobile">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /> 2
                    </svg>
                </button>

                <!-- Profile dropdown -->
                <div class="relative flex-shrink-0" x-data="{ openProfileDropdown: false }" @keydown.escape="openProfileDropdown = false" @click.away="openProfileDropdown = false">

                    <div>
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button id="profileDropDown" @click="openProfileDropdown = !openProfileDropdown" type="button" class="profileDropDown flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        @else
                            <span class="inline-flex rounded-md">
                                    <button id="profileDropDown" @click="openProfileDropdown = !openProfileDropdown" type="button" type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        {{ Auth::user()->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                        @endif
                    </div>

                    <div x-show="openProfileDropdown" x-transition:enter="transition-transform transition-opacity  ease-out duration-100 " x-transition:enter-start="transform opacity-0 scale-95 translate-y-10" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" style="display: none;" class="origin-top-right absolute z-10 right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">

                        @if(session()->has('impersonated_by'))
                            <a href="{{ route('impersonate.leave') }}" class="hover:bg-gray-900 hover:text-white group flex items-center px-2 py-2 text-sm font-regular text-gray-800 transition ease-in-out duration-150">
                                <x-heroicon-o-chevron-left class="h-5 w-5 mr-2" />
                                Super Admin Account
                            </a>
                        @endif

                        <a href="{{ route('profile.show') }}" class="hover:bg-gray-900 hover:text-white group flex items-center px-2 py-2 text-sm font-regular text-gray-800 transition ease-in-out duration-150" role="menuitem" tabindex="-1" id="user-menu-item-0">
                            <x-heroicon-o-user-circle class="h-5 w-5 mr-2" />
                            Your Account
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a id="logout" href="{{ route('logout') }}" class="hover:bg-gray-900 hover:text-white group flex items-center px-2 py-2 text-sm font-regular text-gray-800 transition ease-in-out duration-150" onclick="event.preventDefault();
                                                                 this.closest('form').submit();">
                                <svg class="text-gray-800 group-hover:text-white mr-2 h-5 w-5 transition ease-in-out duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>


