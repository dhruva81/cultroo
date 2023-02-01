<!DOCTYPE html>
{{--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">--}}

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
{{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}

{{--    <title>{{ config('app.name', 'Laravel') }}</title>--}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">--}}
    {{--    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />--}}

{{--    <link rel="stylesheet" href="{{ mix('css/app.css') }}">--}}
{{--    <script src="{{ mix('js/app.js') }}" defer></script>--}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-embed-styles />
    @livewireStyles
    @stack('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css"/>
    <script src="https://sdk.amazonaws.com/js/aws-sdk-2.693.0.min.js"></script>

</head>

<body class="antialiased bg-gray-50">

<div x-cloak x-data="{ sidebarMobile: false, sidebarMini: $persist(false) }">

    @include('layouts.app.sidebar')

    <div
        class="flex flex-col h-screen transition-all ease-in-out duration-200"
        :class="{ 'md:pl-16': sidebarMini, 'md:pl-64': !sidebarMini }"
        x-cloak>

        <main class="flex-grow">

            <!-- topbar -->
            @include('layouts.app.topbar')

            <x-flash/>

            {{ $fullWidth ?? '' }}

            <x-container>
                {{ $slot }}
            </x-container>

        </main>


        <div class="flex-none">
            @include('layouts.app.footer')
        </div>

    </div>

</div>

@livewireScripts
<script src="{{ asset('js/livewire-turbolinks.js') }}" data-turbolinks-eval="false" data-turbo-eval="false"></script>
@stack('modals')
@stack('scripts')
<x-notification/>
@livewire('notifications')
</body>

</html>
