<div class="mt-8 border-b border-gray-200 pb-8">
    <div>
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex sm:space-x-5">
                <div class="flex-shrink-0">
                    <img class="object-cover mx-auto h-32 w-40 rounded-lg" src="{{ $plan->getFirstMediaUrl('icon') }}" alt="">
                </div>

                <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left space-y-2">
                    <p class="text-sm font-medium text-gray-600">
                         #{{ $plan->id }} | {{ $plan->pg_plan_id }}
                    </p>
                    <p class="text-xl font-bold text-gray-900 sm:text-2xl">
                        {{ $plan->meta['name'] }}
                    </p>
                    <p class="text-sm font-medium text-gray-600 flex">
                        {{ $plan->pg_name }} |
                        @if($plan->is_active)
                            <x-heroicon-s-check-circle class="flex-shrink-0 mr-2 h-5 w-5 text-green-400" />
                            Active Plan
                        @else
                            Inactive plan
                        @endif
                    </p>
                </div>
            </div>
            <div class="mt-5 flex justify-center sm:mt-0 space-x-2">
                <x-href href="{{ route('plans.edit', $plan) }}" color="gray">
                    <x-heroicon-o-pencil class="h-4 w-4 -ml-2 mr-2" />
                    Edit
                </x-href>
                <x-href href="{{ route('plans.datatable') }}" color="gray">
                    <x-heroicon-o-chevron-left class="h-4 w-4 -ml-2 mr-2" />
                    All Plans
                </x-href>
            </div>
        </div>
    </div>
</div>


