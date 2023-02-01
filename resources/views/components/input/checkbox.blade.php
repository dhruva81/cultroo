@props([
    'for' => '',
    'label' => '',
    'helpText' => '',
    'isChecked' => false

])

<div class="flex items-start flex-row">
      <div class="flex items-center h-5">
            <input 
                  id="{{ $for }}" 
                  type="checkbox" 
                  {{ $attributes->merge([
                        'class' => 'focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded'
                  ]) }}
                  @if($isChecked)
                  checked="{{ $isChecked }}"
                  @endif
            />
      </div>
      <div class="ml-3 text-sm">
  
            @if($label)
                  <label for="{{ $for }}" class="font-medium text-gray-700">
                       {{ $label }}
                  </label>
            @endif
                  
            @if($helpText)
                  <p class="text-gray-500">{{ $helpText }}</p>
            @endif
  
      </div>
  </div>

  
