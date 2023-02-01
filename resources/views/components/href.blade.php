@props([
'color' => 'indigo',
'href' => '#',
'icon' => '',
'colorVariant' => [
        'default' => 'text-white bg-indigo-600 hover:bg-indigo-700 active:bg-gray-900',
        'indigo' => 'text-white bg-indigo-600 hover:bg-indigo-700 active:bg-gray-900',
        'yellow' => 'text-white bg-yellow-600 hover:bg-yellow-700',
        'blue' => 'text-white bg-blue-600 hover:bg-blue-700',
        'green' => 'text-white bg-green-600 hover:bg-green-700',
        'red' => 'text-white bg-red-600 hover:bg-red-700',
        'gray' => 'text-gray-900 bg-gray-200 hover:bg-gray-300',
        'grey' => 'text-gray-900 bg-gray-200 hover:bg-gray-300',
        'light-blue' => 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200',
        'white' => 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50',
    ],
])
<a href="{{ $href }}" {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 border   shadow-sm text-sm font-medium rounded focus:outline-none ' . $colorVariant[$color]  ]) }}">
      @if($icon)
      @svg($icon, 'h-5 w-5 mr-2')
      @endif
      {{ $slot }}
</a>
