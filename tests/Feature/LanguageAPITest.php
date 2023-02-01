<?php

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('guests can not see list of languages', function () {
    $response = $this
        ->getJson('/api/languages');

    $response->assertStatus(401);
});

it('fetches list of paginated languages for an authenticated user', function () {
    $user = User::factory()->create();
    $languages = Language::factory(3)->create();

    $response = $this
        ->actingAs($user)
        ->getJson('/api/languages');

    $response
        ->assertStatus(200)
        ->assertJsonCount(10, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
            'links',
            'meta'
        ]);

    $this->assertNotNull($response->json('data')[0]['id']);
});


it('ensures that optional limit query parameter is working fine', function () {

    $user = User::factory()->create();
    $collection = Language::factory(6)->create();
    $limit = 5;

    $response = $this
        ->actingAs($user)
        ->getJson('/api/languages?limit=' . $limit);

    $response
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);

    $this->assertNotNull($response->json('data')[0]['id']);
});
