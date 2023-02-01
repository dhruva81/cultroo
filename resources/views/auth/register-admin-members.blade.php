<div>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <div class="mb-8 bg-gray-100 py-2 text-indigo-700 font-semibold text-center rounded shadow">
             Admin Member Registration
        </div>

        <form wire:submit.prevent="submit">

            {{ $this->form }}

            <x-button-loader
                class="my-1 w-full mt-8"
                color="indigo"
                size="lg"
                type="submit"
                wire:target="submit"
            >
                Register
            </x-button-loader>
        </form>
        <div class="mt-8 text-center">
            Already registered?  <a class="underline" href="{{ route('login') }}">Login</a>
        </div>

    </x-jet-authentication-card>



</div>
