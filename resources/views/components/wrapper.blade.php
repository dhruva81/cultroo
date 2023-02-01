@props([
'borderWidth' => 'fullWidth',
])

<x-slot name="fullWidth">

    <div {{ $attributes->merge(['class' => 'pt-10 bg-white']) }}>
        {{ $slot }}


        @if($borderWidth === 'fullWidth')
        <div class="border-t border-gray-200"></div>
        @endif

        @if($borderWidth === 'default')
        <x-container>
            <div class="border-t border-gray-200"></div>
        </x-container>
        @endif

        @if($borderWidth === '7xl')
        <div class="max-w-7xl mx-auto border-t border-gray-200"></div>
        @endif

        @if($borderWidth === 'none')

        @endif

    </div>

</x-slot>