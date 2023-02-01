<?php

use App\Livewire\Customer\BuySubscription;
use Illuminate\Support\Facades\Route;

// This is for testing customer user flow on web

Route::group([
    'middleware' => [
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ],
], function () {
    Route::view('/dashboard', 'customer.dashboard.index')->name('customer.dashboard');

    Route::get('/buy-subscription', BuySubscription::class)->name('customer.subscriptions.buy');
});
