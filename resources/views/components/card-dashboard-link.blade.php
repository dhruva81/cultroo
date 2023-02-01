@props([
'icon' => 'heroicon-o-user',
'title' => 'Title',
'subtitle' => null,
'change' => false,
'href' => '#',
'bgColor' => 'bg-gray-100',
'textColor' => 'text-gray-900',
'hoverColor' => 'bg-gray-200',
'iconColor' => 'bg-purple-500 text-gray-100',
])

<div class="border-b border-gray-200 hover:bg-white pl-4">
    <a href="{{ $href }}" class="focus:outline-none">
        <div class="relative group py-4 flex items-start space-x-3">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-lg {{ $iconColor }}">
                    @svg($icon, "h-6 w-6")
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-medium text-gray-900">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    {{ $title }}
                </div>
                @if(isset($subtitle) && $subtitle)
                <p class="text-sm text-gray-500">
                    {{ $subtitle  }}
                </p>
                @endif
            </div>
            <div class="flex-shrink-0 self-center">
                <!-- Heroicon name: solid/chevron-right -->
                <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </a>
</div>
