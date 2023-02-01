<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <x-header-simple title="Import Data">
            <x-href href="{{ route('dashboard') }}">
                <x-heroicon-o-chevron-left class="h-5 w-5 -ml-2 mr-2" />
                Go Back
            </x-href>
        </x-header-simple>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 w-full mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-exclamation class="h-5 w-5 text-yellow-600" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <a href="#" class="font-medium underline text-yellow-900 hover:text-yellow-700">
                            Click here to download sample excel file.
                        </a>
                    </p>
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-yellow-900">
                            Please note
                        </h3>
                        <div class="mt-2 text-sm text-yellow-800">
                            <ul role="list" class="list-disc pl-5 space-y-1">
                                <li>
                                    The document must be excel document (.xlsx)
                                </li>
                                <li>
                                    The first row of Excel file is heading row.
                                </li>
                                <li>
                                    The heads must be 'series_title, 'series_description', 'episode_title', 'episode_description', 'characters', 'languages', 'tags', 'run_time', 'min_age' and 'status'. (all small letters)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form method="post" action="#" enctype="multipart/form-data">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mt-4">
                Select Excel File
            </label>
            <div class="flex text-sm text-gray-600 my-4">
                <input id="file-upload" name="file" type="file">
            </div>
            <x-button>Submit</x-button>
        </form>
    </div>
</x-app-layout>
