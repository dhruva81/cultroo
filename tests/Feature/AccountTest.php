<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot set account pin', function () {
    $response = $this
        ->postJson('/api/account/pin', [
            'pin' => 1234,
        ]);
    $response
        ->assertStatus(401);
});

test('authenticated users can set 4 digit pin', function () {
    $user = User::factory()->create();
    $response = $this
        ->actingAs($user)
        ->postJson('/api/account/pin', [
            'pin' => 1234,
        ]);
    $response
        ->assertStatus(201);
});

it('throws validation error when pin is invalid', function () {
    $user = User::factory()->create();
    $response = $this
        ->actingAs($user)
        ->postJson('/api/account/pin', [
            'pin' => 123,
        ]);
    $response
        ->assertStatus(422);
});

it('verifies that authenticated user account pin is correct', function () {
    $user = User::factory()->create([
        'pin' => 1234,
    ]);

    $response = $this
        ->actingAs($user)
        ->postJson('/api/account/pin/verify', [
            'pin' => 1234,
        ]);

    $response
        ->assertStatus(200);
});

it('throws validation error for verification of account pin, when the pin is invalid', function () {
    $user = User::factory()->create([
        'pin' => 1234,
    ]);

    $response = $this
        ->actingAs($user)
        ->postJson('/api/account/pin/verify', [
            'pin' => 123,
        ]);

    $response
        ->assertStatus(422);
});

it('throws validation error for verification of account pin , when the pin is incorrect', function () {
    $user = User::factory()->create([
        'pin' => 1234,
    ]);

    $response = $this
        ->actingAs($user)
        ->postJson('/api/account/pin/verify', [
            'pin' => 1235,
        ]);

    $response
        ->assertStatus(422);
});
