<x-header
    title="{{ $video->title }}"
    badge="Video # {{ $video->id }}"
    image="{{ $video->getFirstMediaUrl('thumbnail') }}"
    icon="heroicon-o-collection"
    href="{{ route('videos.show', $video) }}"
>

    <x-slot name="subTitleSlot">
        @if($video->series)
        <nav class="flex" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-4">
                <li>
                    <div class="flex">
                        <a href="{{ route('series.show', $video->series) }}" class="flex text-sm font-medium text-gray-500 hover:text-gray-700">
                            Series : {{ $video->series->title }}
                        </a>
                    </div>
                </li>
            </ol>
        </nav>
        @endif
    </x-slot>

    <x-slot name="actionSlot">

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
                    icon="heroicon-o-pencil-alt"
                    label="Edit Meta"
                    href="{{ route('videos.show', ['video' => $video, 'tab' => 'edit']) }}"
                />

                <x-href-dropdown-link
                    icon="heroicon-o-cloud-upload"
                    label="Replace Video File"
                    href="{{ route('videos.replace',  $video) }}"
                />

                @if($video->series)
                    <x-href-dropdown-link
                        icon="heroicon-o-chevron-left"
                        label="Series Page"
                        href="{{ route('series.show', $video->series) }}"
                    />
                @endif

                @if(!$video->series)
                    <x-href-dropdown-link
                        icon="heroicon-o-chevron-left"
                        label="All shorts"
                        href="{{ route('shorts.datatable') }}"
                    />
                @endif

            </div>
        </div>


    </x-slot>

</x-header>
