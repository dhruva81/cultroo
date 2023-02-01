<div class="mx-auto space-y-8">

    @include('app.collections.collection-header')

    <div class="mt-8">
        <div class="pb-5">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Episodes
            </h3>
            <p class="mt-2 max-w-4xl text-sm text-gray-500">
                List of all episodes in this series.
            </p>
        </div>

        <div class="">
            {{ $this->table }}
        </div>
    </div>


</div>



