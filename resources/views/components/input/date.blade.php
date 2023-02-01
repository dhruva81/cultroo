@props([
'label' => '',
'placeholder' => '',
'value' => '',
'required' => false,
'wclass' => 'mt-4',
'type' => 'text',
'helpText' => false,
])

@if($attributes->whereStartsWith('wire:model')->first())

<div class="{{ $wclass }}">
      @if($label)
      <label 
          for="{{ $attributes['id'] ? $attributes['id'] : $attributes->whereStartsWith('wire:model')->first()}}"
          class="block text-sm font-medium leading-5 text-gray-700">
          {{$label}}
      </label>
      @endif
     
      @if($helpText)
            <div class="text-gray-500 text-xs">{{ $helpText }}</div>
      @endif
      
      <div class="flatpickr mt-2 flex rounded-md shadow-sm">
            <div class="relative flex items-stretch flex-grow focus-within:z-10">
            <input 
                  data-input 
                  {{$attributes->whereStartsWith('wire:model')}} 
                  id="{{$attributes['id'] ? $attributes['id'] : $attributes->whereStartsWith('wire:model')->first()}}"
                  type="{{ $type }}"
                  @error($attributes->whereStartsWith('wire:model')->first())
                        {{ $attributes->merge(['class' => 'py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:text-sm rounded-none rounded-l border-red-500']) }}
                  @else
                        {{ $attributes->merge(['class' => 'py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:text-sm rounded-none rounded-l border-gray-400']) }}
                  @endif
                  placeholder="{{$placeholder}}" 
                  value="{{ $value }}"
                  @error($attributes->whereStartsWith('wire:model')->first())
                  aria-invalid="true"
                  @enderror
                  class="py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-none rounded-l border-gray-400"
                  >
            </div>
            <button 
                  class="input-button pointer-cursor -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-400 text-sm font-medium  text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none" 
                  title="toggle" 
                  type="button"
                  data-toggle>
                  <x-heroicon-s-calendar class="w-5 h-5" />
            </button>
            <button 
                  class="input-button pointer-cursor -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-400 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none" 
                  title="toggle" 
                  type="button"
                  data-clear>
                  <x-heroicon-s-x class="w-5 h-5" />
            </button>
      </div>
      @error($attributes->whereStartsWith('wire:model')->first())
      <p wire:key="error_{{$attributes->whereStartsWith('wire:model')->first()}}" class="mt-2 text-sm text-red-600">
          {{$message}}
      </p>
      @enderror
</div>

@else

<div class="{{ $wclass }}">
      @if($label)
      <label 
          for="{{$attributes['id'] ? $attributes['id'] : $attributes['name'] }}"
          class="block text-sm font-medium leading-5 text-gray-700">
          {{$label}}
      </label>
      @endif
     
      @if($helpText)
            <div class="text-gray-500 text-xs">{{ $helpText }}</div>
      @endif
      
      <div class="flatpickr mt-2 flex rounded-md shadow-sm">
            <div class="relative flex items-stretch flex-grow focus-within:z-10">
            <input 
                  data-input 
                  {{$attributes->whereStartsWith('wire:model')}} 
                  id="{{$attributes['id'] ? $attributes['id'] : $attributes['name'] }}"
                  type="{{ $type }}"
                  @error($attributes['name'])
                        {{ $attributes->merge(['class' => 'py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:text-sm rounded-none rounded-l border-red-500']) }}
                  @else
                        {{ $attributes->merge(['class' => 'py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:text-sm rounded-none rounded-l border-gray-400']) }}
                  @endif
                  placeholder="{{$placeholder}}" 
                  value="{{ $value }}"
                  @error($attributes['name'])
                  aria-invalid="true"
                  @enderror
                  class="py-2.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-none rounded-l border-gray-400"
                  >
            </div>
            <button 
                  class="input-button pointer-cursor -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-400 text-sm font-medium  text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none" 
                  title="toggle" 
                  type="button"
                  data-toggle>
                  <x-heroicon-s-calendar class="w-5 h-5" />
            </button>
            <button 
                  class="input-button pointer-cursor -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-400 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none" 
                  title="toggle" 
                  type="button"
                  data-clear>
                  <x-heroicon-s-x class="w-5 h-5" />
            </button>
      </div>
      @error($attributes['name'])
      <p wire:key="error_{{$attributes->whereStartsWith('wire:model')->first()}}" class="mt-2 text-sm text-red-600">
          {{$message}}
      </p>
      @enderror
</div>


@endif

