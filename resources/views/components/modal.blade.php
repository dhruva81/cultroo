@props(['formAction' => false])

<div>
    @if($formAction)
    <form wire:submit.prevent="{{ $formAction }}">
        @endif
        <div class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150">
            @if(isset($title))
            <div class="text-lg leading-6 font-medium text-gray-900">
                {{ $title }}
            </div>
            @endif
        </div>
        <div class="bg-white px-4">
            <div class="space-y-6">
                {{ $content ?? '' }}
            </div>
        </div>

        <div class="bg-white px-4 pb-5 sm:px-4 mt-4">
            {{ $buttons ?? '' }}
        </div>
        @if($formAction)
    </form>
    @endif
</div>