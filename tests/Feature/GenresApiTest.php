<?php

use App\Models\Genre;
use App\Models\Series;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('does not fetch list of genres for guests', function () {
    $response = $this
        ->getJson('/api/genres');

    $response->assertStatus(401);
});

it('shows list of only published genres in paginated way for an authenticated user', function () {
    $user = User::factory()->create();

    Genre::factory(3)->create([
        'status' => Genre::STATUS_PUBLISHED,
    ]);

    Genre::factory()->create([
        'status' => Genre::STATUS_DRAFT,
    ]);

    Genre::factory()->create([
        'status' => Genre::STATUS_REVIEW,
    ]);

    $response = $this
        ->actingAs($user)
        ->getJson('/api/genres');

    $response
        ->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'icon',
                    'color',
                    'cover_image',
                ],
            ],
            'links',
            'meta'
        ])
    ;

    $this->assertNotNull($response->json('data')[0]['id']);
});

it('ensures that optional limit query parameter in collections is working fine', function () {

    $user = User::factory()->create();
    Genre::factory(5)->create([
        'status' => Genre::STATUS_PUBLISHED,
    ]);

    Genre::factory()->create([
        'status' => Genre::STATUS_DRAFT,
    ]);

    Genre::factory()->create([
        'status' => Genre::STATUS_REVIEW,
    ]);
    $limit = 5;

    $response = $this
        ->actingAs($user)
        ->getJson('/api/genres?limit=' . $limit);

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

it('does not fetch details of a genre for guests', function () {

    $genre = Genre::factory()->create();

    $response = $this
        ->getJson('/api/genres/' . $genre->id);

    $response->assertStatus(401);
});

it('shows details of a genres for an authenticated user', function () {

    $user = User::factory()->create();
    $genre = Genre::factory()->create();

    $response = $this
        ->actingAs($user)
        ->getJson('/api/genres/' . $genre->id);

    $response
        ->assertStatus(200)
        ->assertJsonCount(7, 'data')
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'icon',
                'cover_image',
                'color',
                'series',
                'shorts'
            ],
        ]);

});


// it('fetches videos and series attached to a genre', function () {

//    $user = User::factory()->create();
//    $series = Series::withoutEvents(function () {
//        return Series::factory()->create();
//    });

//    $genre = Genre::factory()
//        ->has(
//            Series::withoutEvents(function () {
//                return Series::factory(2)->create();
//            })
//        )
//        ->create();

//    $genre = Genre::factory()
//        ->has(Series::factory()->count(2))
//        ->create();
//
//    $response = $this
//        ->actingAs($user)
//        ->getJson('/api/genres/' . $genre->id);
//
//    $response
//        ->assertStatus(200)
//        ->assertJsonCount(2, 'data.series')
//        ->assertJsonStructure([
//            'data' => [
//                'id',
//                'name',
//                'icon',
//                'description',
//                'cover_image',
//                'series',
//            ],
//        ])
    ;

//    $this->assertNotEquals($response->json('data.series')[0]['id'], $series->id);
//    $this->assertNotEquals($response->json('data.series')[1]['id'], $series->id);
// });

