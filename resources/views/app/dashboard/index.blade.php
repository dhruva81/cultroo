
    <main class="flex-1 pb-8">

        <div class="mt-8">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-lg leading-6 font-medium text-gray-900">Dashboard</h2>

                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Card -->
                    @foreach($cards as $card)
                        <a href="{{ isset($card['url']) ? $card['url'] : '#'}}" class="bg-white overflow-hidden border border-gray-200 hover:border-indigo-700 group rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if(isset($card['icon']) && $card['icon'])
                                            @svg($card['icon'], 'h-6 w-6 text-gray-400 group-hover:text-indigo-700')
                                        @endif
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 group-hover:text-indigo-900 truncate">
                                                {{ $card['title']  }}
                                            </dt>
                                            <dd class="flex justify-between items-end">
                                                <div class="text-lg font-medium text-gray-900 group-hover:text-indigo-900">
                                                    {{ $card['count'] }}
                                                </div>
                                                @if(isset($card['sub_count']) && $card['sub_count'])
                                                    <div class="text-xs text-gray-600">
                                                        {{ $card['sub_count']  }}
                                                    </div>
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach

                </div>


                @if(false)
                    <div class="mt-8 grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">

                    <div class="border border-gray-200 rounded-lg px-6">
                        <h2 class="mt-4 mb-4 text-base leading-6 font-medium text-gray-900 border-b border-gray-200 pb-4">
                            Most Watched Videos
                        </h2>
                        @foreach($mostWatchedVideos as $video)
                            <div class="relative pb-4">
                                <a href="{{ route('videos.show', $video) }}" class="relative flex items-center space-x-3 group">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center group-hover:text-blue-700">
                                           {{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div class="font-medium text-sm text-gray-900 group-hover:text-blue-700">
                                            <div>
                                                {{ $video->title  }}
                                            </div>
                                            <div class="text-xs text-gray-500 group-hover:text-blue-700">
                                                {{ $video->series?->title  }}
                                            </div>

                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">

                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="border border-gray-200 rounded-md px-6">
                        <h2 class="mt-4 mb-4 text-base leading-6 font-medium text-gray-900 border-b border-gray-200 pb-4">
                            Most Active Users
                        </h2>
                        @foreach($mostActiveUsers as $user)
                            <div class="relative pb-4">
                                <a href="{{ route('users.show', ['user' => $user->user]) }}" class="relative flex  space-x-3 group">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center group-hover:text-blue-700">
                                           {{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div class="font-medium text-sm text-gray-900 group-hover:text-blue-700">
                                            <div>
                                                {{ $user->user?->name  }}
                                            </div>
                                            <div class="text-xs text-gray-500 group-hover:text-blue-700">

                                            </div>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">

                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>


                </div>
                @endif
            </div>

        </div>
    </main>
