@props([
'color' => 'default',
'type' => 'submit',
'size'  => 'default',
'rounded' => 'md',
'colorVariant' => [
'default' =>  'text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm',
'indigo' =>  'text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm',
'yellow' =>  'text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm',
'yellow_light' =>  'text-yellow-700 bg-yellow-200 hover:bg-yellow-300 shadow-sm',
'blue' =>  'text-white bg-blue-600 hover:bg-blue-700 shadow-sm',
'green' =>  'text-white bg-green-600 hover:bg-green-700 shadow-sm',
'red' =>  'text-white bg-red-600 hover:bg-red-700 shadow-sm',
'gray' =>  'text-gray-900 bg-gray-200 hover:bg-gray-300 shadow-sm',
'grey' =>  'text-gray-900 bg-gray-200 hover:bg-gray-300 shadow-sm',
'black' => 'text-white bg-gray-800 hover:bg-gray-700 active:bg-gray-900 shadow-sm',
'white' => 'border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50',
'transparent' => ''
],
'sizeVariant' => [
'default'   =>    'px-4 py-2 text-sm',
'xs'        =>    'px-2.5 py-1.5 text-xs',
'xxs'        =>    'px-1.5 py-1 text-xs',
'md'        =>    'px-4 py-2 text-xs',
'md'        =>    'px-4 py-2 text-sm',
'lg'        =>    'px-4 py-2 text-base',
'xl'        =>    'px-6 py-3 text-base',
],
'roundedVariant' => [
'default'   =>    'rounded',
'sm'        =>  'rounded-sm',
'md'        =>  'rounded-md',
'lg'        =>  'rounded-lg',
'full'      =>    'rounded-full'
]
])

<button {{ $attributes->merge([
        'type' =>       $type,
        'class' =>      $colorVariant[$color] . ' ' .
                        $roundedVariant[$rounded] . ' ' .
                        $sizeVariant[$size]  .
                        'inline-flex items-center border border-transparent font-medium focus:outline-none disabled:opacity-25 transition' ]) }}
        wire:loading.attr="disabled"
>

    <div wire:loading {{ $attributes->only('wire:target') }} >
        <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
    </div>
    <div wire:loading.remove {{ $attributes->only('wire:target') }}>
        {{ $slot }}
    </div>
</button>
