@props([
      'href' => '#',
      'count' => '',
      'icon' => '',
      'label' => ''
])

<a    href="{!! $href !!}"
      {{ $attributes->merge([
            'class' => 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-1 py-2 text-sm font-medium rounded-md'
            ]) }}" >

      @if($icon)
            @svg( $icon, ['class' => 'text-gray-400 group-hover:text-gray-500 flex-shrink-0 ml-1 mr-3 h-5 w-5'])
      @endif
      {{ $label }}
      @if($count)
      <span class="bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full">
            {{ $count }}
      </span>
      @endif
</a>
