<x-header
    title="{{ $genre->name }}"
    badge="Category | #{{ $genre->id }}"
    icon="heroicon-o-adjustments"
>
    <x-slot name="subTitleSlot">

        <dd class="flex items-center text-sm text-gray-500 font-medium capitalize">

            {{--            @if(true)--}}
            {{--                <x-heroicon-s-check-circle class="flex-shrink-0 mr-2 h-5 w-5 text-green-400"/>--}}
            {{--                Published--}}
            {{--            @else--}}
            {{--                Status - Not Published--}}
            {{--            @endif--}}

        </dd>

    </x-slot>

    <x-slot name="actionSlot">

        <div x-data="{ open: false }" class="ml-3 relative">
            <x-button @click="open = !open" @click.away="open = false" aria-haspopup="true"
                      x-bind:aria-expanded="open" class="w-36">
                Actions
                <x-heroicon-o-chevron-down class="-mr-1 ml-2 h-5 w-5 inline"/>
            </x-button>

            <div x-show="open" x-description="Dropdown panel, show/hide based on dropdown state."
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute z-40 lg:origin-top-right px-2 right-0  mt-2 -mr-1 w-60  rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5"
                 role="menu" style="display: none;">

                <x-href-dropdown-link
                    icon="heroicon-o-pencil-alt"
                    label="Edit Genre"
                    href="{{ route('genres.edit', $genre) }}"
                />

                <x-href-dropdown-link
                    icon="heroicon-o-chevron-left"
                    label="Go back"
                    href="{{ route('genres.datatable') }}"
                />
            </div>
        </div>

    </x-slot>

    <x-slot name="navSlot">

        <a href="{{ route('genres.show', ['genre' => $genre, 'tab' => 'series']) }}"
            @class([
            'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm',
            'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'genres.show' && $tab === 'series',
            'border-transparent text-gray-600' => Route::currentRouteName() !== 'genres.show' && $tab === 'series',
         ])>
            <x-heroicon-o-collection
                @class([
                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',
                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'genres.show' && $tab === 'series',
                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'genres.show' && $tab === 'series',
                  ])/>
            Series
        </a>

        <a href="{{ route('genres.show', ['genre' => $genre, 'tab' => 'shorts']) }}"
            @class([
                'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent  font-medium text-sm',
                'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'genres.show' && $tab === 'shorts',
                'border-transparent text-gray-600' => Route::currentRouteName() !== 'genres.show' && $tab === 'shorts',
             ])>
            <x-heroicon-o-video-camera
                @class([
                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',
                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'genres.show' && $tab === 'shorts',
                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'genres.show' && $tab === 'shorts',
                  ])/>
            Shorts
        </a>


        <a href="#"
            @class([
            'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm',
            'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'genres.settings',
            'border-transparent text-gray-600' => Route::currentRouteName() !== 'genres.settings',
         ])>
            <x-heroicon-o-cog
                @class([
                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',
                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'genres.settings',
                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'genres.settings',
                  ])/>
            Settings
        </a>


    </x-slot>

</x-header>
