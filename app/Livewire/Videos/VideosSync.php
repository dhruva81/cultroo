<?php

namespace App\Livewire\Videos;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class VideosSync extends Component
{
    public array $files = [];
    public bool $envVariableNotAvailable = false;

    public function mount()
    {
        $awsDirectVideoUploadsDirectory = config('app.aws_direct_video_uploads_directory');

        if(!$awsDirectVideoUploadsDirectory)
        {
            $this->envVariableNotAvailable = true;
            return;
        }

        $awsFiles = Storage::disk('s3')->allFiles($awsDirectVideoUploadsDirectory);

        foreach($awsFiles as $awsFile)
        {
            $file = pathinfo($awsFile) ;
            $extension = $file['extension'] ?? null;
            $acceptedFileTypes = ['mp4', 'mov'];
            if($extension && in_array($extension, $acceptedFileTypes))
            {
                $this->files[] = $awsFile;
            }
        }
    }

    public function goToVideosCreateFromAWS($file)
    {
        session()->put('aws-video', $file);
        return redirect()->route('videos.create-from-aws');
    }

    public function render()
    {
        return <<<'blade'
        <div class="px-4 sm:px-6 lg:px-8 my-8">

            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-xl font-semibold text-gray-900">
                        Sync Videos from AWS
                    </h1>
                    <p class="mt-2 text-sm text-gray-700">
                        List of videos on AWS which are not synced to the database. These videos are not transcoded yet.
                    </p>
                </div>
            </div>

            @if($envVariableNotAvailable)
                <div class="rounded-md bg-red-50 p-4 my-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                <code>AWS ENVIRONMENT VARIABLE FOR SOURCE VIDOES</code> is not available.
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                Please update your enviornment file or Please ask your tech support team.
                            </div>
                        </div>
                    </div>
                </div>
            @else

            <div>
                <div class="-mx-4 mt-10 ring-1 ring-gray-300 sm:-mx-6 md:mx-0 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($files as $file)
                                <tr>
                                    <td class="relative py-4 pl-4 sm:pl-6 pr-3 text-sm">
                                        <div class="font-medium text-gray-900"> {{ str_replace('uploaded_videos/', '', $file) }}</div>
                                    </td>
                                    <td class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-sm font-medium">
                                        <button wire:click="goToVideosCreateFromAWS('{{ $file}}')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 shadow-sm hover:bg-indigo-700 hover:text-white focus:outline-none">
                                            Create video record
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @endif

        </div>
        blade;
    }
}
