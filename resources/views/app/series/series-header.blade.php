<x-header
    title="{{ $series->title }}"
    badge="Series # {{ $series->id }}"
    image="{{ $series->getFirstMediaUrl('thumbnail') }}"
    icon="heroicon-o-collection"
    href="{{ route('series.show', $series) }}"
>

    <x-slot name="actionSlot">

        <x-href
            icon="heroicon-o-document-text"
            href="{{ route('series.show', ['series' => $series, 'tab' => 'meta']) }}"
            color="white"
        >
            Meta
        </x-href>

        <div x-data="{ open: false }" class="ml-3 relative">
            <x-button @click="open = !open" @click.away="open = false" aria-haspopup="true" x-bind:aria-expanded="open"
                      class="w-36">
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
                    icon="heroicon-o-document-text"
                    label="Add Episodes"
                    href="{{ route('episodes.create', ['series' => $series]) }}"
                />

                <x-href-dropdown-link
                    icon="heroicon-o-pencil"
                    label="Edit Meta"
                    href="{{ route('series.show', ['series' => $series, 'tab' => 'edit']) }}"
                />

                <x-href-dropdown-link
                    icon="heroicon-o-chevron-left"
                    label="Go back"
                    href="{{ route('series.datatable') }}"
                />
            </div>
        </div>

    </x-slot>

    <x-slot name="navSlot">

        {{--        <a href="{{ route('series.show', ['series' => $series, 'tab' => 'meta']) }}"--}}
        {{--            @class([--}}
        {{--            'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm',--}}
        {{--            'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'series.show' && $tab === 'meta',--}}
        {{--            'border-transparent text-gray-600' => Route::currentRouteName() !== 'series.show' && $tab === 'meta',--}}
        {{--         ])>--}}
        {{--            <x-heroicon-o-clipboard-list--}}
        {{--                @class([--}}
        {{--                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',--}}
        {{--                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'series.show' && $tab === 'meta',--}}
        {{--                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'series.show' && $tab === 'meta',--}}
        {{--                  ])/>--}}
        {{--            Meta--}}
        {{--        </a>--}}

        <div class="flex space-x-4">
        <a href="{{ route('series.show', $series) }}"
            @class([
                'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm',
                'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'series.show' && $tab === 'show' && $languageId === null,
                'border-transparent text-gray-600' => Route::currentRouteName() !== 'series.show' && $tab === 'show' && $languageId === null,
             ])>
            <x-heroicon-o-video-camera
                @class([
                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',
                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'series.show' && $tab === 'show' && $languageId === null,
                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'series.show' && $tab === 'show' && $languageId === null,
                  ])/>
            All Episodes
        </a>


        @forelse($series->getLanguages() as $language)

            @if($loop->first)
                    <span class="text-gray-500"> | </span>
            @endif
            <a href="{{ route('series.show', ['series' => $series, 'language' => $language->id]) }}"
                @class([
                    'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm',
                    'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'series.show' && $language->id === $languageId,
                    'border-transparent text-gray-600' => Route::currentRouteName() !== 'series.show' && $language->id === $languageId,
                 ])>
                {{ $language->name }}
            </a>
        @empty

        @endforelse

        </div>

    </x-slot>

</x-header>
