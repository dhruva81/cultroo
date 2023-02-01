<div class="pb-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        User has active subscription.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        User has not purchased any plan.
                    </p>
                </div>
            </div>
        </div>


        <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">

                <div class="w-1/2 form-row">

                    <div class="text-lg font-semibold mb-8">
                        Subscribe to Plan
                    </div>

                    <fieldset>
                        <div class="space-y-8">
                            @forelse($plans as $plan)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            wire:model="planId"
                                            id="standard"
                                            type="radio"
                                            checked
                                            value="{{ $plan->pg_plan_id }}"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="standard" class="font-medium text-gray-700">{{ $plan->pg_name }} -
                                            Rs. {{ $plan->pg_billing_amount }}/{{ $plan->pg_billing_period }}</label>
                                        <p id="standard-description" class="text-gray-500">
                                            {{ ucfirst($plan->pg_billing_period) }} plan  ({{ $plan->pg_plan_id }})
                                        </p>
                                        <p id="standard-description" class="text-gray-500">
                                            {{ isset($plan->meta['name']) ? ucfirst($plan->meta['name']) : 'Not available' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                No Plans found
                            @endforelse
                        </div>
                    </fieldset>

                    <x-jet-button class="mt-4" type="button" wire:click="submit">
                        Subscribe Now
                    </x-jet-button>

                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                window.addEventListener('capturePayment', event => {
                    console.log('capture payment')

                    var options = {
                        "key": @this.razorpay_key,
                        // "amount": @this.amount, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                        // "currency": "INR",
                        "name": "FIC",
                        "description": "Test Transaction",
                        "image": "https://example.com/your_logo",
                        "subscription_id": @this.subscription_id, //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                        "handler": function (response){
                            console.log(response)
                            @this.response = response;
                        // @this.razorpay_payment_id = response.razorpay_payment_id;
                        // @this.callback_razorpay_order_id = response.razorpay_order_id;
                        // @this.razorpay_signature = response.razorpay_signature;
                            Livewire.emit('capturePayment')
                        },
                        "prefill": {
                            "name": "Dinesh Karada",
                            "email": "braineoworks@gmail.com",
                            "contact": "9993171325"
                        },
                        "notes": {
                            "address": "Razorpay Corporate Office"
                        },
                        "theme": {
                            "color": "#3399cc"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.on('payment.failed', function (response){
                        console.log(response);
                    });
                    rzp1.open();

                })
            </script>
        @endpush

        </div>
    </div>
