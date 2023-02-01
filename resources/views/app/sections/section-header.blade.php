<x-header
    title="{{ $section->name }}"
    badge="Section | #{{ $section->id }}"
    icon="heroicon-o-collection"
>
    <x-slot name="subTitleSlot">

        <dd class="flex items-center text-sm text-gray-500 font-medium capitalize">
            <x-heroicon-s-check-circle class="flex-shrink-0 mr-2 h-5 w-5 text-green-400"/>
             A section of {{ $section->model }}s
        </dd>

    </x-slot>

    <x-slot name="actionSlot">

        <x-href
            icon="heroicon-o-document-text"
            href="{{ $section->getAddToSectionLink() }}"
            color="white"
        >
            Add {{ $section->model }}s
        </x-href>

        <div x-data="{ open: false }" class="ml-3 relative">
            <x-button @click="open = !open" @click.away="open = false" aria-haspopup="true"
                      x-bind:aria-expanded="open" class="w-36">
                Actions
                <x-heroicon-o-chevron-down class="-mr-1 ml-2 h-5 w-5 inline"/>
            </x-button>

            <div x-show="open" x-description="Dropdown panel, show/hide based on dropdown state."
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute z-40 lg:origin-top-right px-2 right-0  mt-2 -mr-1 w-60  rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5"
                 role="menu" style="display: none;">

                <x-href-dropdown-link
                    icon="heroicon-o-pencil-alt"
                    label="Edit Section"
                    href="{{ route('sections.edit', $section) }}"
                />

                <x-href-dropdown-link
                    icon="heroicon-o-chevron-left"
                    label="Go back"
                    href="{{ route('sections.datatable') }}"
                />
            </div>
        </div>

    </x-slot>

</x-header>
