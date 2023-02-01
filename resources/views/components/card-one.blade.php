@props([
'icon' => 'heroicon-o-user',
'title' => 'Title',
'count' => '',
'change' => false,
'href' => '#',
'bgColor' => 'bg-gray-100',
'iconColor' => 'text-gray-800',
'textColor' => 'text-gray-900',
'hoverColor' => 'bg-gray-200'
])

<div class="rounded-lg relative text-white shadow-md {{ $bgColor }}  {{ $href !== "#" ? 'hover:' . $hoverColor : '' }} ">
    @if($href !== "#") <a href="{{ $href }}"> @endif
        <div class="w-full px-8 py-8">
            <div class="flex justify-between">
                <div>
                    <p class="font-medium tracking-widest {{ $textColor }}">
                        {{ $title }}
                    </p>
                    @if($count)
                    <p class="text-2xl mt-2 font-semibold {{ $textColor }}">
                        {{ $count }}
                    </p>
                    @endif
                </div>
                <div class="flex items-center">
                    @svg($icon, "h-10 w-10 " . $iconColor )
                </div>
            </div>
        </div>
        @if($href !== "#")
    </a> @endif
</div>