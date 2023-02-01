<?php

use App\Livewire\Collections\CollectionCreateOrUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('ensures that guests cannot access collections create page')
    ->get('/admin/collections/create')
    ->assertStatus(302)
    ->assertRedirect('/login');

it('ensures that guests cannot access collections update page', function () {

    $collection = \App\Models\Kollection::factory()->create();

    $this->get('/admin/collections/' . $collection->id . '/edit')
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('collections create and update page has a collection form')
    ->livewire(CollectionCreateOrUpdate::class)
    ->assertFormExists();

test('collection form has a name, color, status, icon and cover image fields')
    ->livewire(CollectionCreateOrUpdate::class)
    ->assertFormFieldExists('name')
    ->assertFormFieldExists('color')
    ->assertFormFieldExists('status')
    ->assertFormFieldExists('cover_image')
    ->assertFormFieldExists('icon');

it('throws validate error if name and status fields are empty')
    ->livewire(CollectionCreateOrUpdate::class)
    ->set('name', '')
    ->set('status', null)
    ->call('submit')
    ->assertHasErrors(['name' => 'required'])
    ->assertHasErrors(['status' => 'required']);

it('creates a collection', function () {

    livewire(CollectionCreateOrUpdate::class)
        ->set('name', 'Test Collection')
        ->set('color', '#000000')
        ->set('status', 'draft')
        ->call('submit');

    $this->assertDatabaseHas('collections', [
        'name' => 'Test Collection',
        'color' => '#000000',
        'status' => 'draft',
    ]);
});

it('updates a collection', function () {

    $collection = \App\Models\Kollection::factory()->create();

    livewire(CollectionCreateOrUpdate::class, ['collection' => $collection])
        ->set('name', 'New Collection Name')
        ->set('color', $collection->color)
        ->set('status', 'draft')
        ->call('submit');

    $this->assertDatabaseHas('collections', [
        'name' => 'New Collection Name',
    ]);

});


