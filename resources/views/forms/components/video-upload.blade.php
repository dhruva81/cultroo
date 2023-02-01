<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
        <!-- Interact with the `state` property in Alpine.js -->

        @if(!$getLivewire()->isVideoFileAvailable)
            <div
                x-data="{ isUploading: false, progress: 0 }"
                x-on:livewire-upload-start="isUploading = true"
                x-on:livewire-upload-finish="isUploading = false"
                x-on:livewire-upload-error="isUploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
                class="mx-auto max-w-3xl">

                <div class="pb-8">
                    <div class="text-center animate-pulse">
                        <div class="mt-4">
                            <x-heroicon-o-cloud-upload class="mx-auto h-12 w-12 text-gray-400" x-show="isUploading"/>
                        </div>
                        <h2 class="mt-2 text-lg font-medium text-gray-900" x-show="isUploading">Uploading...</h2>
                    </div>
                </div>
                <div class="mb-8" x-show="!isUploading">
                    <label for="myFiles"
                           class="mt-1 cursor-pointer flex justify-center hover:border-indigo-500 rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6 hover:text-indigo-500">
                        <div class="space-y-1 text-center">
                            <x-heroicon-o-cloud-upload class="mx-auto h-12 w-12 text-gray-300"/>
                            <div class="flex justify-center text-sm text-gray-600">
                                <div
                                    class="relative rounded-md text-center bg-white font-medium text-indigo-600 focus-within:outline-none hover:text-indigo-500">
                                    <span>Select video file to upload</span>
                                    <input
                                        onchange="if(this.files[0].type !== 'video/mp4')
                                    {
                                        event.stopImmediatePropagation();
                                        alert('Only video files are allowed');
                                    }"
                                        wire:model="{{ $getStatePath() }}"
                                        id="myFiles"
                                        name="file-upload"
                                        type="file"
                                        class="sr-only">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">File must be in video format only (mp4 etc.)</p>
                        </div>
                    </label>
                </div>

                <div x-show="isUploading" class="flex w-full">
                    <progress max="100" x-bind:value="progress" class="w-full flex-1"></progress>
                </div>
            </div>
        @else
            <div class="mx-auto max-w-lg my-4">
                <div class="py-2">
                    <div class="text-center">
                        <x-heroicon-o-check-circle class="mx-auto h-12 w-12 text-green-600"/>
                        <h2 class="mt-2 text-lg font-medium text-green-700">Video File Uploaded successfully</h2>
                        @if($getLivewire()->video_file)
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $getLivewire()->video_file->getClientOriginalName() }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>

</x-forms::field-wrapper>
