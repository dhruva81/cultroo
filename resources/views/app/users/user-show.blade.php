<div>
    <main class="py-8">

        <div class="max-w-3xl mx-auto lg:max-w-7xl">

            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                <!-- Heroicon name: solid/home -->
                                <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <a href="#"
                               class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Users</a>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="md:flex md:items-center md:justify-between md:space-x-5 border-b border-gray-200 pb-8">

                <div class="flex items-center space-x-5">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <img class="h-16 w-16 rounded-full"
                                 src="https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80"
                                 alt="">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $user->name }}
                        </h1>
                        <div class="text-sm font-medium text-gray-500 space-x-2">
                            <x-badge> Customer </x-badge>
                            <x-badge color="yellow"> Free Account </x-badge>

                        </div>
                    </div>
                </div>
                <div
                    class="mt-6 flex flex-col-reverse justify-stretch space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-x-reverse sm:space-y-0 sm:space-x-3 md:mt-0 md:flex-row md:space-x-3">
                    <a href="{{ route('users.datatable')  }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Go back
                    </a>
                </div>
            </div>

            <div
                class="mt-8 max-w-3xl mx-auto grid grid-cols-1 gap-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
                <div class="space-y-8 lg:col-start-1 lg:col-span-2">

                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Profiles
                        </h3>

                        @if($user->profiles->count() > 0)
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @foreach($user->profiles as $profile)
                                    <div
                                        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <div class="flex-shrink-0">
                                            <img class="h-12 w-12 rounded-full"
                                                 src="{{ asset('assets/images/default-profile.png') }}" alt="">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="focus:outline-none">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $profile->name  }}
                                                </p>
                                                @if($profile->dob)
                                                    <p class="text-sm text-gray-500 truncate">
                                                        {{ \Carbon\Carbon::parse($profile->dob)->age  }} years old
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm">
                                This user has created any profile.
                            </div>
                        @endif
                    </div>


                    <!-- Subscriptions -->
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Active Subscription
                        </h3>

                        <div class="space-y-4 text-xs">
                            Not subscribed to any plan.
                        </div>
                    </div>


                    <!-- Invoices -->
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Invoices
                        </h3>

                        <div class="space-y-4 text-xs">
                            No invoice found!
                        </div>
                    </div>

                    <!-- Watch History -->
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Watch History
                        </h3>

                        <div class="space-y-4 text-xs">
                            No record found!
                        </div>
                    </div>


                    <!-- Bookmarks -->
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Bookmarks
                        </h3>

                        <div class="space-y-4 text-xs">
                            No record found!
                        </div>
                    </div>


                    <!-- Favourites -->
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Favourites
                        </h3>

                        <div class="space-y-4 text-xs">
                            No record found!
                        </div>
                    </div>


                    <!-- Tickets -->
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Support Tickets
                        </h3>

                        <div class="space-y-4 text-xs">
                            No record found!
                        </div>
                    </div>



                </div>

                <section class="lg:col-start-3 lg:col-span-1 space-y-8">

                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Business
                        </h3>
                        <div class="space-y-4 text-xs">
                            No business from this account.
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Account Details
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Email address
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->email  }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Account created at
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->created_at?->format('d M , Y')  }}
                                </dd>
                            </div>

                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Activity Logs
                        </h3>
                        <div class="space-y-4 text-xs">
                            No activity logs for this account.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>
