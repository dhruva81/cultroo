@props([
'title' => '',
'subtitle' => '',
'icon' => '',
'wrapperClass' => 'mt-8 mb-6',
])

<div class="sm:flex sm:items-center {{ $wrapperClass }} ">
    <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900 flex items-center">
            @if($icon)
            @svg($icon, 'h-6 w-6 mr-2 text-gray-500')
            @endif
            {{ $title }}
        </h1>
        @if($subtitle)
        <p class="mt-2 text-sm text-gray-700">
            {{ $subtitle }}
        </p>
        @endif
    </div>
    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        {{ $slot }}
    </div>
</div>
