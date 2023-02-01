@props([
'color' => 'indigo',
'size' => 'small',
'rounded' => 'full',
'withDot' => false,
'colors' => [
    'red' =>  'bg-red-100 text-red-800',
    'yellow' =>  'bg-yellow-100 text-yellow-800',
    'green' =>  'bg-green-100 text-green-800',
    'blue' =>  'bg-blue-100 text-blue-800',
    'indigo' =>  'bg-indigo-100 text-indigo-800',
    'purple' =>  'bg-purple-100 text-purple-800',
    'pink' =>  'bg-pink-100 text-pink-800',
    'gray' =>  'bg-gray-100 text-gray-800',
    'grey' =>  'bg-gray-100 text-gray-800',
],
'sizes' => [
    'small' =>  'px-2.5 py-0.5 text-xs',
    'large' =>  'px-3 py-0.5 text-sm',
],
'roundedVariant' => [
    'full' =>  'rounded-full',
    'lg' =>  'rounded-lg',
    'md' =>  'rounded-md',
    'sm' =>  'rounded-sm',
    'default' =>  'rounded',
    ]
])


<span class="{{  $colors[$color] }}  {{ $sizes[$size] }} {{ $roundedVariant[$rounded] }} inline-flex items-center  font-medium ">
      {{ $slot }}
</span>
