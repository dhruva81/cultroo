@props([
'icon' => 'heroicon-o-user',
'title' => 'Title',
'title' => 'Title',
'count' => '',
'change' => false,
'href' => '#',
'bgColor' => 'bg-gray-100',
'iconColor' => 'text-gray-800',
'textColor' => 'text-gray-900',
'hoverColor' => 'bg-gray-200'
])


<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            @if($icon)
            <div class="flex-shrink-0">
                @svg($icon, "h-6 w-6 text-gray-400")
            </div>
            @endif
            <div class="ml-5 w-0 flex-1">
                <dl>
                    @if($title)
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            {{ $title }}
                        </dt>
                    @endif
                    @if($count)
                        <dd>
                            <div class="text-lg font-medium text-gray-900">
                                {{ $count  }}
                            </div>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    @if($href)
    <div class="bg-gray-50 px-5 py-3">
        <div class="text-sm">
            <a href="{{ $href  }}" class="font-medium text-cyan-700 hover:text-cyan-900"> View all </a>
        </div>
    </div>
        @endif
</div>
