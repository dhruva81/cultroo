@props([
'color' => 'primary',
'type' => 'submit',
'size' => 'default',
'rounded' => 'md',

'colorVariant' => [
    'primary' => 'text-white bg-indigo-600 hover:bg-indigo-700',
    'white' => 'text-gray-700 bg-gray-200 hover:bg-gray-300',
    'indigo' => 'text-white bg-indigo-600 hover:bg-indigo-700',
    'yellow' => 'text-white bg-yellow-600 hover:bg-yellow-700',
    'blue' => 'text-white bg-blue-600 hover:bg-blue-700',
    'green' => 'text-white bg-green-600 hover:bg-green-700',
    'red' => 'text-white bg-red-600 hover:bg-red-700',
    'gray' => 'text-gray-900 bg-gray-300 hover:bg-gray-400',
    'grey' => 'text-gray-900 bg-gray-300 hover:bg-gray-400',
],

'sizeVariant' => [
    'default' => 'px-3 py-1.5 text-xs',
    'xs' => 'px-4 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-xs',
    'lg' => 'px-4 py-2 text-base',
    'xl' => 'px-6 py-3 text-base',
],

'roundedVariant' => [
    'default' => 'rounded',
    'sm' => 'rounded-sm',
    'md' => 'rounded-md',
    'lg' => 'rounded-lg',
    'full' => 'rounded-full'
]
])

<button {{ $attributes->merge([
        'type' => $type,
        'class' =>      $colorVariant[$color] . ' ' .
                        $roundedVariant[$rounded] . ' ' .
                        $sizeVariant[$size]  .
                        'inline-flex items-center border border-transparent shadow-sm font-medium focus:outline-none disabled:opacity-25 transition' ]) }}">

{{ $slot }}
</button>
