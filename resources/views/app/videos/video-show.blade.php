
<div>

    @include('app.videos.video-header')

    @php
        $colors = [
                'bg-red-100 text-red-800',
                'bg-yellow-100 text-yellow-800',
                'bg-green-100 text-green-800',
                'bg-blue-100 text-blue-800',
                'bg-indigo-100 text-indigo-800',
                'bg-purple-100 text-purple-800',
                'bg-pink-100 text-pink-800'
            ]
    @endphp
    <div class="mx-auto mt-8 grid max-w-3xl grid-cols-1 gap-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2 lg:col-start-1">

            <!-- Short Description -->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    @if($video->getFirstMediaUrl('video'))
                        <video width="100%"  controls>
                            <source src="{{ $video->getFirstMediaUrl('video')  }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        @elseif($video->getVideoUrForAdmin())
                            <video width="100%"  controls>
                                <source src="{{ $video->getVideoUrForAdmin()  }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                        <div class="py-8">
                            <x-empty-state
                                title="Video not available"
                                icon="heroicon-o-exclamation"
                            />
                        </div>
                    @endif
                </div>
            </section>

            <!-- Transcoding Status -->
            <!-- Description -->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">
                            Description
                        </h2>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">

                        @if(!empty($video->description))
                            <div class="prose max-w-none text-gray-700 text-sm">
                                {!! $video->description !!}
                            </div>
                        @else
                            <x-empty-state
                                title="Description not available!"
                                icon="heroicon-o-document-text"
                            />
                        @endif

                    </div>
                </div>
            </section>

            <!-- Tags -->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">
                            Tags
                        </h2>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        @forelse($video->tags as $tag)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ \Illuminate\Support\Arr::random($colors)  }}"> {{ $tag['slug'] }} </span>
                        @empty
                            <x-empty-state
                                title="No tags attached to the video!"
                                icon="heroicon-o-tag"
                            />
                        @endforelse

                    </div>
                </div>
            </section>

            <!-- Characters -->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">
                            Characters
                        </h2>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        @forelse($video->characters as $character)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ \Illuminate\Support\Arr::random($colors)  }}"> {{ $character['name'] }} </span>
                        @empty
                            <x-empty-state
                                title="No characters attached to the video!"
                                icon="heroicon-o-exclamation-circle"
                            />
                        @endforelse
                    </div>
                </div>
            </section>


            <!-- Subtitles -->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">
                            Subtitles
                        </h2>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <ul role="list" class="divide-y divide-gray-200 rounded-md border border-gray-200">
                            @forelse($video->getSubtitles() as $key =>  $subtitle)
                            <li class="flex items-center justify-between py-3 pl-3 pr-4 text-sm">
                                <div class="flex w-0 flex-1 items-center">
                                    <!-- Heroicon name: mini/paper-clip -->
                                    <svg class="h-5 w-5 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.451a.75.75 0 111.061 1.06l-3.45 3.451a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2 w-0 flex-1 truncate">
                                        {{ ucfirst($key) }}
                                    </span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ $subtitle  }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">Download</a>
                                </div>
                            </li>
                            @empty
                                <x-empty-state
                                    title="No subtitles available for the video!"
                                    icon="heroicon-o-exclamation-circle"
                                />
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6 lg:col-span-1 lg:col-start-3">

            <section>
                <div class="rounded-lg bg-white overflow-hidden shadow">
                    <img src="{{ $video->getFirstMediaUrl('thumbnail') }}"/>
                </div>
            </section>

            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">
                            Stats
                        </h2>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <div class="space-y-4">

                            @if($video->file_name)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    File Name
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->file_name }}
                                </dd>
                            </div>
                            @endif

                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Transcoding Status
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->getTranscodingStatus() }}
                                </dd>
                            </div>

                            @if($video->getTranscodingStatus() === 'Complete')
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Available in
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->getAvailableFormats() }}
                                </dd>
                            </div>
                            @endif

                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Status
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->getVideoStatus($video->status) }}
                                </dd>
                            </div>


                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Language
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->language?->name ?? '-'}}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Created at
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->created_at->format('d M Y') }}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Total Views
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->watch_count }} views
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">
                                    Min Age
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $video->min_age }} years
                                </dd>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>



    {{--<div class="lg:flex lg:items-center lg:justify-between my-8">--}}
{{--    <div class="flex-1 min-w-0">--}}
{{--        @if($video->series)--}}
{{--        <nav class="flex" aria-label="Breadcrumb">--}}
{{--            <ol role="list" class="flex items-center space-x-4">--}}
{{--                <li>--}}
{{--                    <div class="flex">--}}
{{--                        <a href="{{ route('series.show', $video->series) }}" class="flex text-sm font-medium text-gray-500 hover:text-gray-700">--}}
{{--                           <x-heroicon-o-arrow-narrow-left class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />--}}
{{--                            {{ $video->series->title }}--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </li>--}}
{{--            </ol>--}}
{{--        </nav>--}}
{{--        @endif--}}
{{--        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">--}}
{{--            {{ $video->title  }}--}}
{{--        </h2>--}}
{{--        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">--}}
{{--            <div class="mt-2 flex items-center text-sm text-gray-500">--}}
{{--                <x-heroicon-s-calendar class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />--}}
{{--                Published on July 7, 2022--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="mt-5 flex lg:mt-0 lg:ml-4">--}}
{{--    <span class="sm:block">--}}

{{--       <a href="{{ route('videos.edit', ['video' => $video]) }}"--}}
{{--          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">--}}
{{--                  <x-heroicon-s-pencil class="-ml-1 mr-2 h-5 w-5 text-gray-500" />--}}
{{--                Edit--}}
{{--       </a>--}}

{{--        @if($video->series)--}}
{{--          <a href="{{ route('series.show', $video->series) }}"--}}
{{--                 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">--}}
{{--              <x-heroicon-s-chevron-left class="-ml-1 mr-2 h-5 w-5 text-gray-500" />--}}
{{--            Back to series--}}
{{--          </a>--}}
{{--        @endif--}}

{{--    </span>--}}

{{--    </div>--}}
{{--</div>--}}


</div>


