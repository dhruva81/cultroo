<div class="p-6">

    @php
        $colors = [
               // 'bg-red-100 text-red-800',
               // 'bg-yellow-100 text-yellow-800',
                'bg-green-100 text-green-800',
                'bg-blue-100 text-blue-800',
                'bg-indigo-100 text-indigo-800',
               // 'bg-purple-100 text-purple-800',
               // 'bg-pink-100 text-pink-800'
            ]
    @endphp

    @forelse($records as $record)
        <div
            class="inline-flex items-center my-1 px-2.5 py-0.5 rounded-md text-sm font-medium {{ \Illuminate\Support\Arr::random($colors)  }}">
            {{ $record['slug'] }}
            <span class="ml-2">
                ({{ $record['videos_count'] + $record['series_count'] }})
            </span>
        </div>
    @empty

    @endforelse

</div>
