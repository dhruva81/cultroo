@props([
    'title' => '',
    'subtitle' => '',
    'icon' => '',
    'iconBgColor' => 'bg-gray-200',
    'href' => '',
    'buttonIcon' => '',
    'buttonText' => '',
    'plainHref' => '',
    'wrapperClass' => '',
])

<div class="text-center py-8 {{ $wrapperClass }} ">

    @if($icon)
        @svg($icon, "mx-auto h-10 w-10 text-gray-400")
    @endif

    <h3 class="mt-4 text-sm font-medium text-gray-900">
        {{ $title ?? ' '}}
    </h3>

    <p class="mt-1 text-sm text-gray-500">
        {{ $subtitle ?? ' '}}
    </p>


   @if($href)
        <div class="mt-6">
            <a href="{{ $href }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
               Click Here
            </a>
        </div>
    @endif


</div>
