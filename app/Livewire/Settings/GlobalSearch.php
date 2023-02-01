<?php

namespace App\Livewire\Settings;

use App\Models\Series;
use App\Models\Video;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalSearch extends Component
{

    public $search;

    public $series = null;
    public $videos = null;

    public $newResults = [];
    public $newResultsCount = 0;

    public function mount()
    {
        $this->search = '';
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->series = Series::select('id', 'title', 'uuid')->where('title', 'like', '%' . $this->search . '%')->take(10)->get();
            $this->videos = Video::select('id', 'title', 'uuid')->where('title', 'like', '%' . $this->search . '%')->take(10)->get();

            foreach($this->series as $series) {
                $this->newResults[] = [
                    'title' => $series->title,
                    'url' => route('series.show', $series->uuid),
                    'type' => 'Series'
                ];
            }

            foreach($this->videos as $video) {
                $this->newResults[] = [
                    'title' => $video->title,
                    'url' => route('videos.show', $video->uuid),
                    'type' => 'Video',
                ];
            }

            $this->newResultsCount = count($this->newResults);
        }
    }

    public function render()
    {
        return <<<'blade'
                <div
                    x-data="{
                            open: false,
                            selected: null,
                            newResultsCount: $wire.entangle('newResultsCount'),
                            keyDown() {
                                 if(this.selected < this.newResultsCount) {
                                    this.selected++;
                                 } else {
                                    this.selected = 1;
                                 }
                            },
                            keyUp() {
                                if(this.selected > 1) {
                                    this.selected--;
                                } else {
                                    this.selected = this.newResultsCount;
                                }
                            },
                        }"
                    @click.away="open = false"
                    class="w-full max-w-3xl flex md:ml-0">
                    <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
                            <svg class="ml-2 flex-shrink-0 h-5 w-5" x-description="Heroicon name: solid/search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                <input
                    name="search_field"
                    id="search_field"
                    wire:model.debounce.500ms="search"
                    @click="open=true"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none sm:text-sm"
                    placeholder="Search"
                    type="search"
                    x-ref="search"
                    @keyup.down="keyDown()"
                    @keyup.up="keyUp()"
                    @focus="open = true"
                    @keydown="open = true"
                    @keydown.escape.window="open = false"
                    @keydown.shift.tab="open = false"
                    x-on:keydown.enter.prevent="window.location.href = $wire.newResults[selected].url"
                    >
                    <div class="absolute inset-y-0 right-6 flex items-center">
                        <x-loader />
                    </div>

                    @if(strlen($this->search) > 2)
                    <div
                        class="absolute z-10 bg-white w-full rounded-t-none rounded shadow-lg max-w-full"
                        x-show.transition="open"
                        >

                        <ul class="divide-y divide-gray-200">

                        @if($newResults)
                            @foreach($newResults as $srs)
                            <li class="px-4 hover:bg-blue-100" x-bind:class="{   ' bg-blue-100 ' : selected === {{ $loop->index + 1  }} }">
                                <a href="{{ $srs['url'] }}">
                                    <div class="flex space-x-3 py-2">
                                        <div class="flex-1 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-600">
                                                {{ $srs['title'] }}
                                                </h3>
                                             <x-badge color="yellow"> {{ $srs['type'] }} </x-badge>
                                        </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach

                        @endif


                       </ul>

                    </div>
                    @endif
                </div>
            </div>
        blade;
    }

//    public function render()
//    {
//        return <<<'blade'
//             <div class="max-w-lg w-full lg:max-w-xs">
//                        <label for="search" class="sr-only">Search</label>
//                        <div class="relative">
//                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
//                                <!-- Heroicon name: solid/search -->
//                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
//                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
//                                </svg>
//                            </div>
//                            <input id="search" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search" type="search">
//                        </div>
//                    </div>
//        blade;
//    }
}
