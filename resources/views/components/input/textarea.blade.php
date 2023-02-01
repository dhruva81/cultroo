@props([
'icon' => '',
'label' => '',
'placeholder' => '',
'required' => false,
'value' => '',
'wclass' => 'mt-4'
])



@if($attributes->whereStartsWith('wire:model')->first())


<div class="{{ $wclass }}">
    <label for="{{$attributes->whereStartsWith('wire:model')->first()}}"
        class="block text-sm font-medium leading-5 text-gray-700">{{$label}}
    </label>
    <div class="mt-2 relative rounded-md shadow-sm">
        <textarea {{$attributes->whereStartsWith('wire:model')}}
            id="{{$attributes->whereStartsWith('wire:model')->first()}}" rows="3" @error($attributes->whereStartsWith('wire:model')->first())
            {{ $attributes->merge(['class' => 'block w-full pr-10 border-red-500 text-red-900 placeholder-red-300 focus:outline-none   sm:text-sm rounded-sm']) }}
            @else
            {{ $attributes->merge(['class' => 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-400 rounded-sm']) }}
            @endif 
            placeholder="{{$placeholder}}"
            @error($attributes->whereStartsWith('wire:model')->first())
            aria-invalid="true"
            @enderror
        >{{ $value }}</textarea>
        @error($attributes->whereStartsWith('wire:model')->first())
        <div wire:key="error_svg_{{$attributes->whereStartsWith('wire:model')->first()}}"
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        @enderror
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
    <label for="{{$attributes['id'] ? $attributes['id'] : $attributes['name'] }}"
        class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>
    @endif
    <div class="relative mt-2 rounded-md shadow-sm">
        <textarea rows="3" id="{{$attributes['id'] ? $attributes['id'] : $attributes['name'] }}"
            @error($attributes['name'])
            {{ $attributes->merge(['class' => 'block w-full pr-10 border-red-500 text-red-900 placeholder-red-300 focus:outline-none sm:text-sm rounded-sm']) }}
            @else
            {{ $attributes->merge(['class' => 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-400 rounded-sm']) }}
            @endif placeholder="{{$placeholder}}">{{ $value }}</textarea>
        @error($attributes['name'])
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        @enderror
    </div>
    @error($attributes['name'])
    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@endif
