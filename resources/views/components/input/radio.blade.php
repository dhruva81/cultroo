@props([
    'for' => '',
    'label' => '',
    'helpText' => '',
])

<div class="relative flex items-start">
    <div class="flex items-center h-5">
      <input 
        id="{{ $for }}" 
        type="radio" 
        {{ $attributes->merge([
            'class' => 'focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300'
        ]) }}
        >
    </div>
    <div class="ml-3 text-sm">

        @if($label)
        <label for="{{ $for }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
        @endif
            
        @if($helpText)
            <p class="text-gray-500">{{ $helpText }}</p>
        @endif

    </div>
</div>


 
