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

        <!-- Description -->
        <section>
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-medium leading-6 text-gray-900">
                        Description
                    </h2>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">

                    @if(!empty($series->description))
                        <div class="prose max-w-none text-gray-700 text-sm">
                            {!! $series->description !!}
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


        <!-- Short Description -->
        <section>
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-medium leading-6 text-gray-900">
                        Synopsis
                    </h2>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">

                    @if(!empty($series->synopsis))
                        <div class="prose max-w-none text-gray-700 text-sm">
                            {!! $series->synopsis !!}
                        </div>
                    @else
                        <x-empty-state
                            title="Short description not available!"
                            icon="heroicon-o-document"
                        />
                    @endif

                </div>
            </div>
        </section>

        <!-- Language -->
        <section>
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-medium leading-6 text-gray-900">
                        Languages
                    </h2>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6 text-gray-700 text-sm">
                    {{ $series->getLanguages()?->pluck('name')?->implode(', ')  }}
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
                    @forelse($series->tags as $tag)
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ \Illuminate\Support\Arr::random($colors)  }}"> {{ $tag['slug'] }} </span>
                    @empty
                        <x-empty-state
                            title="No tags attached to this series!"
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
                    @forelse($series->characters as $character)
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ \Illuminate\Support\Arr::random($colors)  }}"> {{ $character['name'] }} </span>
                    @empty
                        <x-empty-state
                            title="No characters attached to this series!"
                            icon="heroicon-o-exclamation-circle"
                        />
                    @endforelse
                </div>
            </div>
        </section>

    </div>

    <!-- Sidebar -->
    <div class="space-y-6 lg:col-span-1 lg:col-start-3">

        <section>
            <div class="rounded-lg bg-white overflow-hidden shadow">
                <img src="{{ $series->getFirstMediaUrl('thumbnail') }}"/>
            </div>
        </section>

        <section>
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-medium leading-6 text-gray-900">
                        Stats
                    </h2>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6 text-gray-700 text-sm">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">
                                Total Episodes
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $series->videos->count() }} episodes
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">
                                Total Views
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $series->videos->sum('watch_count') }} views
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">
                                Min Age
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $series->min_age }} years
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">
                                Max Age
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $series->max_age ?? '-'}}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

