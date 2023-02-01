<?php

use App\Models\Kollection;
use App\Models\Series;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not fetch list of collections for guests', function () {
    $response = $this
        ->getJson('/api/collections');

    $response->assertStatus(401);
});

it('shows list of paginated collections for an authenticated user', function () {
    $user = User::factory()->create();
    $collection = Kollection::factory(3)->create();

    $response = $this
        ->actingAs($user)
        ->getJson('/api/collections');

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
        ]);

    $this->assertNotNull($response->json('data')[0]['id']);
});

it('ensures that optional limit query parameter in collections is working fine', function () {

    $user = User::factory()->create();
    $collection = Kollection::factory(6)->create();
    $limit = 5;

    $response = $this
        ->actingAs($user)
        ->getJson('/api/collections?limit=' . $limit);

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

it('does not fetch details of a collection for guests', function () {

    $collection = Kollection::factory()->create();

    $response = $this
        ->getJson('/api/collections/' . $collection->id);

    $response->assertStatus(401);
});

it('shows details of a collection for an authenticated user', function () {

    $user = User::factory()->create();
    $collection = Kollection::factory()->create();

    $response = $this
        ->actingAs($user)
        ->getJson('/api/collections/' . $collection->id);

    $response
        ->assertStatus(200)
        ->assertJsonCount(8, 'data')
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'icon',
                'cover_image',
                'color',
                'shorts',
                'series',
            ],
        ]);

});

//
//it('fetches shorts and series attached to a collection', function () {
//    $user = User::factory()->create();
//    $series = Series::withoutEvents(function () {
//        return Series::factory()->create();
//    });
//    $video = Video::withoutEvents(function () {
//        return Video::factory()->create();
//    });
//
////    $video = Video::factory()->create();
////    $series = Series::factory()->create();
//
//    $collection = Kollection::factory()
//        ->has(Video::factory()->count(3))
//        ->has(Series::factory()->count(2))
//        ->create();
//
//    $response = $this
//        ->actingAs($user)
//        ->getJson('/api/collections/' . $collection->id);
//
//    $response
//        ->assertStatus(200)
//        ->assertJsonCount(3, 'data.shorts')
//        ->assertJsonCount(2, 'data.series')
//        ->assertJsonStructure([
//            'data' => [
//                'id',
//                'name',
//                'cover_image',
//                'color',
//                'shorts',
//                'series',
//            ],
//        ]);
//
//    $this->assertNotEquals($response->json('data.shorts')[0]['id'], $video->id);
//    $this->assertNotEquals($response->json('data.shorts')[1]['id'], $video->id);
//    $this->assertNotEquals($response->json('data.shorts')[2]['id'], $video->id);
//    $this->assertNotEquals($response->json('data.series')[0]['id'], $series->id);
//    $this->assertNotEquals($response->json('data.series')[1]['id'], $series->id);
//});
//
