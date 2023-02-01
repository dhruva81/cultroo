@props([
    'id' => '',
    'icon' => '',
    'image' => '',
    'title' => '',
    'badge' => '',
    'iconColor' => 'text-black',
    'iconBgColor' => 'bg-gray-200',
    'wrapperClass' => 'bg-white',
    'href' => '',
])

<x-slot name="fullWidth">
    <div {{ $attributes->merge(['class' => 'pt-5 mb-8 border-b border-gray-200 ' . $wrapperClass]) }}>
        <div class="px-4 md:px-6 lg:px-8">

            <!-- Header Slot -->
            <div class="sm:flex sm:items-center sm:justify-between pb-5">
                    <div class="flex-1 flex items-center space-x-4">

                            @if($image)
                            <div class="flex-shrink-0">
                                <img class="object-cover mx-auto h-24 w-32 rounded-lg"
                                     src="{{ $image }}" alt="">
                            </div>
                            @elseif($icon)
                                <div class="bg-gray-200 p-4 flex items-center justify-center rounded-lg">
                                @svg($icon, "h-6 w-6 " . $iconColor )
                                </div>
                            @endif


                        <div>
                            @if($badge)
                                <div
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $badge }}
                                </div>
                            @endif

                                <h2 class="mt-1 text-xl font-semibold text-gray-900 sm:truncate">
                                    @if($href)
                                        <a href="{{ $href }}" >
                                            {{ $title }}
                                        </a>
                                    @else
                                        {{ $title }}
                                    @endif
                                </h2>

                            <div class="mt-1">
                                {{ $subTitleSlot ?? '' }}
                            </div>

                        </div>
                    </div>

                    <div class="mt-5 flex justify-center sm:mt-0 space-x-3">
                        {{ $actionSlot ?? '' }}
                    </div>

                </div>

            <!-- Navslot  -->
            @isset($navSlot)
                <div class="relative pt-3 border-t border-gray-200">
                    <div class="sm:block">
                        <nav class="flex space-x-8">
                            {{ $navSlot ?? '' }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-slot>
