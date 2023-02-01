@props([
    'label' => '',
    'name' => '',
    'helpText' => false,
    'inline' => false,
])

<div class="mt-4">
      @if($label)
            <legend class="block text-sm font-medium leading-5 text-gray-700">
            {{ $label }}
            </legend>

            @if($helpText)
                <div class="text-gray-500 text-sm">{{ $helpText }}</div>
            @endif
            
      @endif

      @if($inline)

      <div class="mt-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200">
            {{ $slot }}
      </div>
     
      @else

      <div class="mt-4 space-y-4">
            {{ $slot }}
      </div>

      @endif

      @error($name)
        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
      @enderror

</div>

