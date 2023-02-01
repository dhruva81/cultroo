<div
    x-data="{ open: @entangle('showSeriesModal').defer }"
    x-show="open"
    class="relative z-10"
    aria-labelledby="slide-over-title"
    role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-xl">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     x-description="Slide-over panel, show/hide based on slide-over state."
                     @click.away="open = false"
                     class="transition pointer-events-auto w-screen max-w-xl">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white">
                        <div class="flex-1">
                            @if($series)
                                <!-- Header -->
                                <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                                    <div class="flex items-start justify-between space-x-3">
                                        <div class="space-y-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        Series ID # {{ $series->id}}
                                                    </span>
                                            <h2 class="text-lg font-medium text-white" id="slide-over-title">
                                                {{ $series->title }}
                                            </h2>
                                        </div>
                                        <div class="flex h-7 items-center">
                                            <button x-on:click="open = false" type="button"
                                                    class="text-indigo-200 hover:text-white">
                                                <span class="sr-only">Close panel</span>
                                                <!-- Heroicon name: outline/x -->
                                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                     aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider container -->
                                <div class="space-y-6 py-6 px-4">
                                    <div
                                        @class([
                                            'rounded-md py-4 px-4',
                                             'bg-green-50' => $series->badge_status == 'published',
                                             'bg-red-50' => $series->badge_status == 'published_not_visible',
                                            'bg-blue-50' => $series->badge_status == 'draft',
                                            'bg-yellow-50' => $series->badge_status == 'review',
                                        ])>
                                        <div class="flex">
                                            <div class="flex-shrink-0">

                                                @if($series->badge_status == 'published_not_visible')
                                                    <x-heroicon-s-exclamation-circle class="h-5 w-5 text-red-400"/>
                                                @elseif($series->badge_status == 'published')
                                                    <x-heroicon-s-check-circle class="h-5 w-5 text-green-400"/>
                                                @elseif($series->badge_status == 'draft')
                                                    <x-heroicon-s-information-circle class="h-5 w-5 text-blue-400"/>
                                                @elseif($series->badge_status == 'review')
                                                    <x-heroicon-s-information-circle class="h-5 w-5 text-yellow-400"/>
                                                @endif

                                            </div>
                                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                                <p
                                                    @class([
                                                          'text-sm',
                                                          'text-green-700' => $series->badge_status == 'published',
                                                          'text-red-700' => $series->badge_status == 'published_not_visible',
                                                          'text-blue-700' => $series->badge_status == 'draft',
                                                          'text-yellow-700' => $series->badge_status == 'review',
                                                  ])>

                                                    @if($series->badge_status == 'published_not_visible')
                                                        This series is published but not visible to the public.
                                                    @else
                                                        Status - {{ $series->badge_status }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white">
                                        <h3 class="px-1 text-base font-bold leading-none text-gray-900 dark:text-white">
                                            Stats
                                        </h3>
                                        @foreach($items as $item)
                                            @if($item['visible'])
                                                <div class="p-1">
                                                    {{ $item['label'] ?? '' }} - {{ $item['value'] ?? '' }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="bg-white">
                                        <h3 class="px-1 text-base font-bold leading-none text-gray-900 dark:text-white">
                                            Seasons
                                        </h3>
                                        @foreach($series->getSeasons() as $series)
                                            <div class="p-1">
                                                Season # {{ $series->season }} - {{ $series->title }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action buttons -->
                        <div x-on:click="open = false" class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                            <div class="flex justify-end space-x-3">
                                <button x-on:click="open = false" type="button"
                                        class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
