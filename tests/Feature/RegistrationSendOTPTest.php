<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('otp successfully sent to new email address', function () {
    $response = $this->postJson('/api/register/send-otp', [
        'email' => 'sumit@gmail.com',
    ]);

    $response
        ->assertStatus(201);
});
