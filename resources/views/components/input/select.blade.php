@props([
    'label' => '',
    'placeholder' => null,
    'name' => '',
    'helpText' => false,
])

<div class="mt-4">
  @if($label)
  <label class="block text-sm font-medium leading-5 text-gray-700">{{ $label }}</label>
  @endif
  
  @if($helpText)
    <div class="text-gray-500 text-sm">{{ $helpText }}</div>
  @endif

  <select name="{{ $name }}"  {{ $attributes->merge(['class' => 'mt-1 block w-full pl-3 pr-10 py-2.5 text-base border-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded']) }}>
    @if ($placeholder)
        <option disabled value="">{{ $placeholder }}</option>
    @endif

    {{ $slot }}

  </select>

  @error($name)
    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
  @enderror

</div>
