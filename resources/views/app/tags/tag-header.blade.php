<x-header
    title="{{ $tag->name }}"
    badge="Tag | #{{ $tag->id }}"
    icon="heroicon-o-tag"
>
    <x-slot name="actionSlot">


    </x-slot>

    <x-slot name="navSlot">

        <a href="{{ route('tags.show', $tag->slug) }}"
            @class([
                'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm',
                'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'tags.show' && $tab === 'videos',
                'border-transparent text-gray-600' => Route::currentRouteName() !== 'tags.show' && $tab === 'videos',
             ])>
            <x-heroicon-o-video-camera
                @class([
                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',
                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'tags.show' && $tab === 'videos',
                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'tags.show' && $tab === 'videos',
                  ])/>
            Shorts
        </a>


        <a href="{{ route('tags.show', ['tag' => $tag->slug, 'tab' => 'series']) }}"
            @class([
            'flex items-center hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm',
            'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'tags.show' && $tab === 'series',
            'border-transparent text-gray-600' => Route::currentRouteName() !== 'tags.show' && $tab === 'series',
         ])>
            <x-heroicon-o-collection
                @class([
                     'group-hover:text-gray-500 -ml-0.5 mr-2 h-5 w-5',
                     'border-indigo-500 text-indigo-600' => Route::currentRouteName() === 'tags.show' && $tab === 'series',
                     'border-transparent text-gray-600' => Route::currentRouteName() !== 'tags.show' && $tab === 'series',
                  ])/>
            Series
        </a>

    </x-slot>

</x-header>
