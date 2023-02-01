@props([
'title' => '',
'subtitle' => '',
'headings' => [],
'rows' => [],
'dropdown' => false,
'wrapperClass' => 'mb-8',
])

<div class="{{ $wrapperClass }}">
    <div class="sm:flex sm:items-center">
        @if($title)
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">{{ $title}}</h1>
            @if($subtitle)
            <p class="mt-2 text-sm text-gray-700"> {{ $subtitle }} </p>
            @endif
        </div>
        @endif
    </div>

    <div class="-mx-4 mt-4 ring-1 ring-gray-300 sm:-mx-6 md:mx-0 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">

            @if(count($headings) > 0)
            <thead>
                <tr>
                    @foreach($headings as $heading)
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 whitespace-nowrap">
                        {{ $heading }}
                    </th>
                    @endforeach
                    @if(isset($row['actions']) && count($row['actions']) > 0)
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Select</span>
                    </th>
                    @endif
                </tr>
            </thead>
            @endif

            @if(isset($head))
            <thead>
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            @endif

            @if(isset($body))
            <tbody class="divide-y divide-gray-200">
                {{ $body ?? ''}}
            </tbody>
            @else
            <tbody class="divide-y divide-gray-200">
                @forelse($rows as $row)
                <tr>
                    @foreach($row['data'] as $r)
                    <td class="relative py-4 pl-4 sm:pl-6 pr-3 text-sm">
                        {{ $r }}
                    </td>
                    @endforeach

                    @if(isset($row['actions']) && count($row['actions']) > 0)
                    <td class="relative py-4 pl-4 sm:pl-6 pr-3 text-sm">

                        @if(!$dropdown)
                        <div>
                            @foreach($row['actions'] as $action)
                            <a href="{{ $action['href'] }}" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ $action['label'] }}
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div x-data="{ open: false }" @keydown.escape.stop="open = false" @click.away="open = false" class="relative flex justify-end items-center">
                            <button type="button" class="w-8 h-8 bg-white inline-flex items-center justify-center text-gray-400 rounded-full hover:text-gray-500 focus:outline-none" @click="open = !open" aria-haspopup="true" x-bind:aria-expanded="open">
                                <span class="sr-only">Open options</span>
                                <svg class="w-5 h-5" x-description="Heroicon name: solid/dots-vertical" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                    </path>
                                </svg>
                            </button>

                            <div x-description="Dropdown menu, show/hide based on menu state." x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mx-3 origin-top-right absolute right-7 top-0 w-48 mt-1 rounded-md shadow-lg z-10 bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-200 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="project-options-menu-0" style="display: none;">
                                @foreach($row['actions'] as $action)
                                <div class="py-1" role="none">
                                    <a href="{{ $action['href'] }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                        @if($action['icon'])
                                        @svg($action['icon'], 'mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500')
                                        @endif
                                        {{ $action['label'] }}
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td class="relative py-4 pl-4 sm:pl-6 pr-3 text-sm border-t border-gray-200">
                        Nothing found!
                    </td>
                </tr>
                @endforelse
            </tbody>
            @endif
        </table>
    </div>
</div>